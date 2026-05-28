<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\GbpSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GbpSync extends Command
{
    protected $signature   = 'metrics:sync-gbp
        {--project= : Sync just one project ID}
        {--month=   : Month to sync (Y-m). Defaults to previous month.}';
    protected $description = 'Pull Google Business Profile metrics (website clicks, calls, directions, impressions) into project_metrics for agency-mode projects.';

    public function handle(): int
    {
        $month = $this->option('month') ? Carbon::parse($this->option('month'))->startOfMonth() : now()->subMonth()->startOfMonth();

        $query = Project::query()->whereNotNull('gbp_location_id')
            ->whereHas('workspace', fn ($w) => $w->where('is_agency_mode', true));

        if ($id = $this->option('project')) {
            $query->where('id', $id);
        }

        $projects = $query->get();
        $this->info("Syncing GBP for {$projects->count()} project(s) — {$month->format('F Y')}");

        $ok = 0; $failed = 0;
        foreach ($projects as $project) {
            $result = GbpSyncService::syncProject($project, $month);
            if ($result === null) {
                $this->warn("  - {$project->title}: FAILED (see log)");
                $failed++;
            } else {
                $this->line("  ✓ {$project->title}: clicks={$result['gmb_clicks']} · calls={$result['gmb_calls']} · directions={$result['gmb_directions']} · impressions={$result['gmb_impressions']}");
                $ok++;
            }
        }

        $this->info("Done. ok=$ok failed=$failed");
        return self::SUCCESS;
    }
}
