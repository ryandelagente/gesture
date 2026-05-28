<?php

namespace App\Console\Commands;

use App\Models\Bug;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BugsRetentionCleanup extends Command
{
    protected $signature = 'bugs:retention-cleanup {--dry-run : Show what would be deleted without deleting}';
    protected $description = 'Delete bugs (and their screenshots/videos) older than each project\'s configured retention window.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $totalDeleted = 0;
        $totalFiles = 0;

        Project::whereNotNull('bug_retention_days')->where('bug_retention_days', '>', 0)->chunk(50, function ($projects) use (&$totalDeleted, &$totalFiles, $dry) {
            foreach ($projects as $project) {
                $cutoff = now()->subDays((int) $project->bug_retention_days);
                $query = Bug::where('project_id', $project->id)->where('created_at', '<', $cutoff);
                if ($project->retention_widget_only) {
                    $query->where('source', 'widget');
                }

                $count = (clone $query)->count();
                if ($count === 0) continue;

                $this->info("Project #{$project->id} ({$project->title}): {$count} bug(s) past {$project->bug_retention_days}d cutoff");

                $query->chunk(200, function ($bugs) use (&$totalDeleted, &$totalFiles, $dry) {
                    foreach ($bugs as $bug) {
                        if ($bug->screenshot_path) {
                            if (!$dry) { Storage::disk('public')->delete($bug->screenshot_path); }
                            $totalFiles++;
                        }
                        if ($bug->video_path) {
                            if (!$dry) { Storage::disk('public')->delete($bug->video_path); }
                            $totalFiles++;
                        }
                        if (!$dry) { $bug->delete(); }
                        $totalDeleted++;
                    }
                });
            }
        });

        $label = $dry ? '(dry-run) Would delete' : 'Deleted';
        $this->info("$label {$totalDeleted} bug(s) and {$totalFiles} file(s).");
        return self::SUCCESS;
    }
}
