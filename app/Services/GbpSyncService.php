<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectMetric;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GbpSyncService
{
    private const SCOPES = ['https://www.googleapis.com/auth/business.manage'];
    private const API    = 'https://businessprofileperformance.googleapis.com/v1';

    /**
     * Pull monthly Business Profile metrics for one project:
     *   WEBSITE_CLICKS, CALL_CLICKS, BUSINESS_DIRECTION_REQUESTS,
     *   BUSINESS_IMPRESSIONS_DESKTOP_SEARCH + MOBILE_SEARCH + DESKTOP_MAPS + MOBILE_MAPS
     *
     * Saves totals into project_metrics with source='gbp':
     *   gmb_clicks       — website clicks
     *   gmb_calls        — call clicks
     *   gmb_directions   — direction requests
     *   gmb_impressions  — sum across all four impression buckets
     */
    public static function syncProject(Project $project, ?Carbon $monthStart = null): ?array
    {
        if (empty($project->gbp_location_id)) {
            return null;
        }
        $credsPath = config('services.google.credentials_path');
        if (!$credsPath || !file_exists($credsPath)) {
            Log::warning('GBP sync skipped: credentials missing', ['project_id' => $project->id]);
            return null;
        }

        $monthStart = $monthStart ? $monthStart->copy()->startOfMonth() : now()->subMonth()->startOfMonth();
        $monthEnd   = (clone $monthStart)->endOfMonth();

        // Accept "1234567890" OR "accounts/x/locations/1234567890" — extract the numeric ID
        $locId = $project->gbp_location_id;
        if (preg_match('#locations/([^/]+)#', $locId, $m)) {
            $locId = $m[1];
        }

        try {
            $token = self::accessToken($credsPath);

            $metricsList = [
                'WEBSITE_CLICKS',
                'CALL_CLICKS',
                'BUSINESS_DIRECTION_REQUESTS',
                'BUSINESS_IMPRESSIONS_DESKTOP_SEARCH',
                'BUSINESS_IMPRESSIONS_MOBILE_SEARCH',
                'BUSINESS_IMPRESSIONS_DESKTOP_MAPS',
                'BUSINESS_IMPRESSIONS_MOBILE_MAPS',
            ];

            $params = [
                'dailyRange.start_date.year'  => $monthStart->year,
                'dailyRange.start_date.month' => $monthStart->month,
                'dailyRange.start_date.day'   => $monthStart->day,
                'dailyRange.end_date.year'    => $monthEnd->year,
                'dailyRange.end_date.month'   => $monthEnd->month,
                'dailyRange.end_date.day'     => $monthEnd->day,
            ];

            $totals = array_fill_keys($metricsList, 0);
            // The API uses repeated `dailyMetrics=` query params — Guzzle handles arrays correctly with this key style
            $url = self::API . '/locations/' . urlencode($locId) . ':fetchMultiDailyMetricsTimeSeries';
            // Build query string manually because `dailyMetrics` is repeated
            $qs = http_build_query($params);
            foreach ($metricsList as $m) {
                $qs .= '&dailyMetrics=' . $m;
            }

            $resp = Http::withToken($token)->timeout(20)->get($url . '?' . $qs);
            if (!$resp->successful()) {
                throw new \RuntimeException('GBP API ' . $resp->status() . ': ' . $resp->body());
            }

            // Response: { multiDailyMetricTimeSeries: [ { dailyMetricTimeSeries: [ { dailyMetric, timeSeries:{ datedValues:[{date,value}] } } ] } ] }
            foreach ($resp->json('multiDailyMetricTimeSeries', []) as $group) {
                foreach (($group['dailyMetricTimeSeries'] ?? []) as $series) {
                    $key = $series['dailyMetric'] ?? null;
                    if (!$key || !isset($totals[$key])) continue;
                    foreach (($series['timeSeries']['datedValues'] ?? []) as $row) {
                        $totals[$key] += (int) ($row['value'] ?? 0);
                    }
                }
            }

            $clicks      = $totals['WEBSITE_CLICKS']              ?? 0;
            $calls       = $totals['CALL_CLICKS']                 ?? 0;
            $directions  = $totals['BUSINESS_DIRECTION_REQUESTS'] ?? 0;
            $impressions = ($totals['BUSINESS_IMPRESSIONS_DESKTOP_SEARCH'] ?? 0)
                         + ($totals['BUSINESS_IMPRESSIONS_MOBILE_SEARCH']  ?? 0)
                         + ($totals['BUSINESS_IMPRESSIONS_DESKTOP_MAPS']   ?? 0)
                         + ($totals['BUSINESS_IMPRESSIONS_MOBILE_MAPS']    ?? 0);

            $write = [
                'gmb_clicks'       => $clicks,
                'gmb_calls'        => $calls,
                'gmb_directions'   => $directions,
                'gmb_impressions'  => $impressions,
            ];
            foreach ($write as $key => $value) {
                ProjectMetric::updateOrCreate(
                    ['project_id' => $project->id, 'period_start' => $monthStart, 'metric_key' => $key],
                    ['period_end' => $monthEnd, 'metric_value' => $value, 'source' => 'gbp']
                );
            }

            return $write;
        } catch (\Throwable $e) {
            Log::error('GBP sync failed', [
                'project_id'  => $project->id,
                'location_id' => $project->gbp_location_id,
                'error'       => $e->getMessage(),
            ]);
            return null;
        }
    }

    private static function accessToken(string $credsPath): string
    {
        $creds = new ServiceAccountCredentials(self::SCOPES, $credsPath);
        $token = $creds->fetchAuthToken();
        if (empty($token['access_token'])) {
            throw new \RuntimeException('Failed to fetch GBP access token');
        }
        return $token['access_token'];
    }
}
