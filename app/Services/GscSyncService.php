<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectKeywordRanking;
use App\Models\ProjectMetric;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GscSyncService
{
    private const SCOPES = ['https://www.googleapis.com/auth/webmasters.readonly'];
    private const API   = 'https://searchconsole.googleapis.com/webmasters/v3';

    /**
     * Pull top queries + impressions/clicks for one project for the given month.
     * - Writes per-keyword rows into project_keyword_rankings (current vs previous month).
     * - Bucket assignment: position 1-3 → top3, 4-10 (improving) → progressing, else long_tail.
     * - Also writes a "gsc_clicks" / "gsc_impressions" total into project_metrics.
     */
    public static function syncProject(Project $project, ?Carbon $monthStart = null): ?array
    {
        if (empty($project->gsc_site_url)) {
            return null;
        }
        $credsPath = config('services.google.credentials_path');
        if (!$credsPath || !file_exists($credsPath)) {
            Log::warning('GSC sync skipped: credentials missing', ['project_id' => $project->id]);
            return null;
        }

        $monthStart = $monthStart ? $monthStart->copy()->startOfMonth() : now()->subMonth()->startOfMonth();
        $monthEnd   = (clone $monthStart)->endOfMonth();
        $prevStart  = (clone $monthStart)->subMonth()->startOfMonth();
        $prevEnd    = (clone $prevStart)->endOfMonth();

        try {
            $token = self::accessToken($credsPath);
            $siteUrl = $project->gsc_site_url;

            $current  = self::fetchQueries($token, $siteUrl, $monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d'));
            $previous = self::fetchQueries($token, $siteUrl, $prevStart->format('Y-m-d'),  $prevEnd->format('Y-m-d'));

            // Map previous positions by keyword for quick lookup
            $prevByKw = [];
            foreach ($previous as $row) {
                $prevByKw[$row['keyword']] = $row['position'];
            }

            // Replace this project's rankings for the period
            ProjectKeywordRanking::where('project_id', $project->id)
                ->where('period_start', $monthStart->toDateString())
                ->delete();

            $totalClicks = 0; $totalImpressions = 0;
            foreach ($current as $row) {
                $pos = (int) round($row['position']);
                $bucket = $pos <= 3 ? 'top3' : ($pos <= 10 ? 'progressing' : 'long_tail');
                ProjectKeywordRanking::create([
                    'project_id'        => $project->id,
                    'period_start'      => $monthStart,
                    'keyword'           => $row['keyword'],
                    'position'          => max(1, min(100, $pos)),
                    'previous_position' => isset($prevByKw[$row['keyword']]) ? max(1, min(100, (int) round($prevByKw[$row['keyword']]))) : null,
                    'bucket'            => $bucket,
                ]);
                $totalClicks      += (int) $row['clicks'];
                $totalImpressions += (int) $row['impressions'];
            }

            // Store totals into project_metrics
            foreach ([
                'gsc_clicks'      => $totalClicks,
                'gsc_impressions' => $totalImpressions,
            ] as $key => $value) {
                ProjectMetric::updateOrCreate(
                    ['project_id' => $project->id, 'period_start' => $monthStart, 'metric_key' => $key],
                    ['period_end' => $monthEnd, 'metric_value' => $value, 'source' => 'gsc']
                );
            }

            return [
                'keywords_synced' => count($current),
                'clicks'          => $totalClicks,
                'impressions'     => $totalImpressions,
            ];
        } catch (\Throwable $e) {
            Log::error('GSC sync failed', [
                'project_id' => $project->id,
                'site_url'   => $project->gsc_site_url,
                'error'      => $e->getMessage(),
            ]);
            return null;
        }
    }

    private static function accessToken(string $credsPath): string
    {
        $creds = new ServiceAccountCredentials(self::SCOPES, $credsPath);
        $token = $creds->fetchAuthToken();
        if (empty($token['access_token'])) {
            throw new \RuntimeException('Failed to fetch GSC access token');
        }
        return $token['access_token'];
    }

    private static function fetchQueries(string $token, string $siteUrl, string $start, string $end, int $rowLimit = 50): array
    {
        $url = self::API . '/sites/' . urlencode($siteUrl) . '/searchAnalytics/query';
        $resp = Http::withToken($token)->timeout(20)->post($url, [
            'startDate'  => $start,
            'endDate'    => $end,
            'dimensions' => ['query'],
            'rowLimit'   => $rowLimit,
            'dataState'  => 'final',
        ]);
        if (!$resp->successful()) {
            throw new \RuntimeException('GSC API ' . $resp->status() . ': ' . $resp->body());
        }
        $rows = [];
        foreach ($resp->json('rows', []) as $r) {
            $rows[] = [
                'keyword'     => $r['keys'][0] ?? '',
                'clicks'      => $r['clicks'] ?? 0,
                'impressions' => $r['impressions'] ?? 0,
                'position'    => $r['position'] ?? 0,
            ];
        }
        return $rows;
    }
}
