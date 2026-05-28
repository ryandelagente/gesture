<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\Ga4SyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class Ga4Sync extends Command
{
    protected $signature   = 'metrics:sync-ga4
        {--project= : Sync just one project ID}
        {--month=   : Month to sync (Y-m). Defaults to previous month.}';
    protected $description = 'Pull GA4 metrics (sessions, users, page_views, leads) into project_metrics for agency-mode projects.';

    public function handle(): int
    {
        $month = $this->option('month') ? Carbon::parse($this->option('month'))->startOfMonth() : now()->subMonth()->startOfMonth();

        $query = Project::query()->whereNotNull('ga4_property_id')
            ->whereHas('workspace', fn ($w) => $w->where('is_agency_mode', true));

        if ($id = $this->option('project')) {
            $query->where('id', $id);
        }

        $projects = $query->get();
        $this->info("Syncing {$projects->count()} project(s) for {$month->format('F Y')}...");

        $ok = 0; $failed = 0; $skipped = 0;
        foreach ($projects as $project) {
            $result = Ga4SyncService::syncProject($project, $month);
            if ($result === null) {
                $this->warn("  - {$project->title}: FAILED or skipped (check log)");
                $failed++;
            } else {
                $bits = collect($result)->map(fn ($v, $k) => "$k=$v")->implode(', ');
                $this->line("  ✓ {$project->title}: $bits");
                $ok++;
            }
        }

        $this->info("Done. ok=$ok failed=$failed");
        return self::SUCCESS;
    }
}
