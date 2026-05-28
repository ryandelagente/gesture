<?php

namespace App\Observers;

use App\Mail\BugAssignedToYou;
use App\Mail\BugStatusChangedToReporter;
use App\Models\Bug;
use App\Models\BugStatus;
use App\Models\ProjectSlaPolicy;
use App\Models\User;
use App\Services\BugAiTriage;
use App\Services\BugWebhookDispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BugObserver
{
    public function creating(Bug $bug): void
    {
        // SLA: compute due_at from project SLA + priority
        if (empty($bug->due_at) && $bug->project_id && $bug->priority) {
            $sla = ProjectSlaPolicy::lookup($bug->project_id, $bug->priority);
            $bug->due_at = now()->addHours($sla['resolve_hours']);
        }
    }

    public function created(Bug $bug): void
    {
        $this->notifyAssignee($bug, (int) $bug->assigned_to);
        BugWebhookDispatcher::dispatch('bug.created', $bug->loadMissing('project', 'bugStatus'));

        // AI triage suggestions for widget-source bugs (background-safe; no-op if no API key)
        if ($bug->source === 'widget' && empty($bug->ai_suggestions)) {
            $sug = BugAiTriage::suggest($bug);
            if ($sug) {
                // Use updateQuietly so this doesn't re-trigger the observer
                $bug->updateQuietly(['ai_suggestions' => $sug]);
            }
        }
    }

    public function updated(Bug $bug): void
    {
        if ($bug->wasChanged('bug_status_id')) {
            $oldStatusId = $bug->getOriginal('bug_status_id');
            $oldStatus = $oldStatusId ? optional(BugStatus::find($oldStatusId))->name : 'Unknown';
            $newStatus = optional($bug->bugStatus()->first())->name ?? 'Unknown';
            $this->emailReporterOnStatusChange($bug, $oldStatus, $newStatus);
            BugWebhookDispatcher::dispatch('bug.status_changed', $bug->loadMissing('project'), [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);

            // SLA: stamp resolved_at when moving to a resolved-style status
            if (empty($bug->resolved_at) && in_array($newStatus, ['Resolved', 'Closed', 'Done'])) {
                $bug->updateQuietly(['resolved_at' => now()]);
            }
        }
        if ($bug->wasChanged('assigned_to')) {
            $this->notifyAssignee($bug, (int) $bug->assigned_to);
            BugWebhookDispatcher::dispatch('bug.assigned', $bug->loadMissing('project'));
        }
        if ($bug->wasChanged('priority') && !$bug->resolved_at) {
            // Recompute due_at if priority changed
            $sla = ProjectSlaPolicy::lookup($bug->project_id, $bug->priority);
            $newDue = ($bug->created_at ?? now())->addHours($sla['resolve_hours']);
            $bug->updateQuietly(['due_at' => $newDue]);
        }
    }

    private function emailReporterOnStatusChange(Bug $bug, string $oldStatus, string $newStatus): void
    {
        if (empty($bug->guest_email)) {
            return;
        }
        try {
            Mail::to($bug->guest_email)->send(new BugStatusChangedToReporter($bug, $oldStatus, $newStatus));
        } catch (\Throwable $e) {
            Log::warning('Bug status auto-reply failed', ['bug_id' => $bug->id, 'error' => $e->getMessage()]);
        }
    }

    private function notifyAssignee(Bug $bug, int $assigneeId): void
    {
        if (!$assigneeId) return;
        $assignee = User::find($assigneeId);
        if (!$assignee || empty($assignee->email)) return;

        try {
            Mail::to($assignee->email)->send(new BugAssignedToYou($bug->loadMissing('project', 'bugStatus'), $assignee));
        } catch (\Throwable $e) {
            Log::warning('Bug assigned notification failed', ['bug_id' => $bug->id, 'assignee' => $assigneeId, 'error' => $e->getMessage()]);
        }
    }
}
