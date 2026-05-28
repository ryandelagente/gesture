<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\GscSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GscSync extends Command
{
    protected $signature   = 'metrics:sync-gsc
        {--project= : Sync just one project ID}
        {--month=   : Month to sync (Y-m). Defaults to previous month.}';
    protected $description = 'Pull GSC keyword rankings + clicks/impressions into project_keyword_rankings & project_metrics for agency-mode projects.';

    public function handle(): int
    {
        $month = $this->option('month') ? Carbon::parse($this->option('month'))->startOfMonth() : now()->subMonth()->startOfMonth();

        $query = Project::query()->whereNotNull('gsc_site_url')
            ->whereHas('workspace', fn ($w) => $w->where('is_agency_mode', true));

        if ($id = $this->option('project')) {
            $query->where('id', $id);
        }

        $projects = $query->get();
        $this->info("Syncing GSC for {$projects->count()} project(s) — {$month->format('F Y')}");

        $ok = 0; $failed = 0;
        foreach ($projects as $project) {
            $result = GscSyncService::syncProject($project, $month);
            if ($result === null) {
                $this->warn("  - {$project->title}: FAILED (see log)");
                $failed++;
            } else {
                $this->line("  ✓ {$project->title}: {$result['keywords_synced']} kw · {$result['clicks']} clicks · {$result['impressions']} impressions");
                $ok++;
            }
        }

        $this->info("Done. ok=$ok failed=$failed");
        return self::SUCCESS;
    }
}
