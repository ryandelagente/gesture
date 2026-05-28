<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectMetric;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Ga4SyncService
{
    /**
     * Fetch core metrics (sessions, users, page_views, leads) for one project
     * for a given calendar month. Writes results into `project_metrics` and
     * returns the rows it wrote (or null on failure).
     */
    public static function syncProject(Project $project, ?Carbon $monthStart = null): ?array
    {
        if (empty($project->ga4_property_id)) {
            return null;
        }
        $credsPath = config('services.google.credentials_path') ?: env('GOOGLE_APPLICATION_CREDENTIALS');
        if (!$credsPath || !file_exists($credsPath)) {
            Log::warning('GA4 sync skipped: GOOGLE_APPLICATION_CREDENTIALS not set or file missing');
            return null;
        }

        $monthStart = $monthStart ? $monthStart->copy()->startOfMonth() : now()->subMonth()->startOfMonth();
        $monthEnd   = (clone $monthStart)->endOfMonth();
        $leadEvent  = $project->lead_event_name ?? config('services.google.default_lead_event', 'generate_lead');

        try {
            $client = new BetaAnalyticsDataClient([
                'credentials' => $credsPath,
            ]);

            // 1. Sessions / users / page_views in one request
            $report = $client->runReport(new RunReportRequest([
                'property'    => 'properties/' . $project->ga4_property_id,
                'date_ranges' => [new DateRange([
                    'start_date' => $monthStart->format('Y-m-d'),
                    'end_date'   => $monthEnd->format('Y-m-d'),
                ])],
                'metrics'     => [
                    new Metric(['name' => 'sessions']),
                    new Metric(['name' => 'totalUsers']),
                    new Metric(['name' => 'screenPageViews']),
                ],
            ]));

            $sessions = 0; $users = 0; $views = 0;
            foreach ($report->getRows() as $row) {
                $vals = $row->getMetricValues();
                $sessions += (int) $vals[0]->getValue();
                $users    += (int) $vals[1]->getValue();
                $views    += (int) $vals[2]->getValue();
            }

            // 2. Leads = count of the configured lead event
            $leadsResp = $client->runReport(new RunReportRequest([
                'property'      => 'properties/' . $project->ga4_property_id,
                'date_ranges'   => [new DateRange([
                    'start_date' => $monthStart->format('Y-m-d'),
                    'end_date'   => $monthEnd->format('Y-m-d'),
                ])],
                'dimensions'    => [new Dimension(['name' => 'eventName'])],
                'metrics'       => [new Metric(['name' => 'eventCount'])],
            ]));

            $leads = 0;
            foreach ($leadsResp->getRows() as $row) {
                $dim = $row->getDimensionValues()[0]->getValue();
                $val = (int) $row->getMetricValues()[0]->getValue();
                if (in_array($dim, ['generate_lead', 'form_submit', 'phone_click', $leadEvent], true)) {
                    $leads += $val;
                }
            }
            // 3. AI-referral sessions: group by sessionSource, filter to known AI hosts.
            $aiSessions = 0; $aiUsers = 0;
            try {
                $aiResp = $client->runReport(new RunReportRequest([
                    'property'    => 'properties/' . $project->ga4_property_id,
                    'date_ranges' => [new DateRange([
                        'start_date' => $monthStart->format('Y-m-d'),
                        'end_date'   => $monthEnd->format('Y-m-d'),
                    ])],
                    'dimensions'  => [new Dimension(['name' => 'sessionSource'])],
                    'metrics'     => [
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'totalUsers']),
                    ],
                ]));
                $aiHosts = ProjectMetric::AI_SOURCE_HOSTS;
                foreach ($aiResp->getRows() as $row) {
                    $source = strtolower($row->getDimensionValues()[0]->getValue());
                    foreach ($aiHosts as $needle) {
                        if (str_contains($source, $needle)) {
                            $aiSessions += (int) $row->getMetricValues()[0]->getValue();
                            $aiUsers    += (int) $row->getMetricValues()[1]->getValue();
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('GA4 AI-referral query failed', ['error' => $e->getMessage()]);
            }

            $client->close();

            $written = [];
            foreach ([
                'sessions'             => $sessions,
                'users'                => $users,
                'page_views'           => $views,
                'leads'                => $leads,
                'ai_referral_sessions' => $aiSessions,
                'ai_referral_users'    => $aiUsers,
            ] as $key => $value) {
                ProjectMetric::updateOrCreate(
                    ['project_id' => $project->id, 'period_start' => $monthStart, 'metric_key' => $key],
                    ['period_end' => $monthEnd, 'metric_value' => $value, 'source' => 'ga4']
                );
                $written[$key] = $value;
            }

            return $written;
        } catch (\Throwable $e) {
            Log::error('GA4 sync failed', [
                'project_id'  => $project->id,
                'property_id' => $project->ga4_property_id,
                'error'       => $e->getMessage(),
            ]);
            return null;
        }
    }
}
