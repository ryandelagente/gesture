<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStage;

class OnboardingService
{
    /**
     * Apply the onboarding template that matches the project's service type.
     * Returns the number of tasks created. Idempotent: tasks already on the
     * project (by title) are skipped, so re-applying won't duplicate.
     */
    public static function apply(Project $project, ?int $byUserId = null): int
    {
        $serviceTypes = array_filter(array_map('trim', explode(',', (string) ($project->description ?? ''))));
        if (empty($serviceTypes)) {
            $serviceTypes = ['Web Development'];
        }

        $templates = config('onboarding');
        $stageId   = self::resolveStageId($project->workspace_id);
        if (!$stageId) {
            return 0;
        }

        $existing    = Task::where('project_id', $project->id)->pluck('title')->all();
        $startDate   = $project->start_date ?? now();
        $createdById = $byUserId ?? $project->created_by ?? 1;
        $created = 0;

        // Map each service-type template to a task category so the cross-project
        // overview ("Content Tasks") can split work between web build vs. content.
        $categoryMap = [
            'Web Development' => 'web',
            'SEO'             => 'content',
            'Google Ads'      => 'general',
        ];

        foreach ($serviceTypes as $serviceType) {
            $template = $templates[$serviceType] ?? null;
            if (!$template) continue;
            $defaultCategory = $categoryMap[$serviceType] ?? 'general';

            foreach ($template as $i => $row) {
                if (in_array($row['title'], $existing, true)) continue;
                $offset = (int) ($row['offset_days'] ?? ($i * 2));
                Task::create([
                    'project_id'    => $project->id,
                    'task_stage_id' => $stageId,
                    'title'         => $row['title'],
                    'description'   => $row['description'] ?? null,
                    'priority'      => $row['priority'] ?? 'medium',
                    'category'      => $row['category'] ?? $defaultCategory,
                    'start_date'    => $startDate,
                    'end_date'      => (clone $startDate)->addDays($offset),
                    'progress'      => 0,
                    'created_by'    => $createdById,
                ]);
                $existing[] = $row['title']; // dedupe across multiple templates
                $created++;
            }
        }

        return $created;
    }

    private static function resolveStageId(int $workspaceId): ?int
    {
        $target = config('onboarding.initial_stage', 'To Do');
        $stage = TaskStage::where('workspace_id', $workspaceId)->where('name', $target)->first()
            ?? TaskStage::where('workspace_id', $workspaceId)->where('is_default', true)->first()
            ?? TaskStage::where('workspace_id', $workspaceId)->orderBy('order')->first();
        return $stage?->id;
    }
}
