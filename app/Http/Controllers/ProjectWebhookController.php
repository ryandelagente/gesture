<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\Project;
use App\Models\ProjectWebhook;
use App\Services\BugWebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectWebhookController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize($project);
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'target_url' => 'required|url|max:512',
            'platform'   => 'nullable|in:auto,slack,teams,discord,generic',
            'events'     => 'nullable|array',
        ]);
        $data['project_id'] = $project->id;
        $data['created_by'] = Auth::id();
        $data['events'] = $request->input('events', ['bug.created', 'bug.assigned', 'bug.status_changed']);
        $data['platform'] = $data['platform'] ?? 'auto';
        $data['is_enabled'] = true;
        ProjectWebhook::create($data);
        return back()->with('status', 'Webhook added.');
    }

    public function destroy(Project $project, ProjectWebhook $webhook)
    {
        $this->authorize($project);
        abort_if($webhook->project_id !== $project->id, 404);
        $webhook->delete();
        return back()->with('status', 'Webhook removed.');
    }

    public function toggle(Project $project, ProjectWebhook $webhook)
    {
        $this->authorize($project);
        abort_if($webhook->project_id !== $project->id, 404);
        $webhook->is_enabled = !$webhook->is_enabled;
        $webhook->fail_count = 0;
        $webhook->save();
        return back()->with('status', $webhook->is_enabled ? 'Enabled.' : 'Disabled.');
    }

    public function test(Project $project, ProjectWebhook $webhook)
    {
        $this->authorize($project);
        abort_if($webhook->project_id !== $project->id, 404);

        $latestBug = Bug::where('project_id', $project->id)->latest()->first();
        if (!$latestBug) {
            return back()->with('status', 'Submit a bug first so the webhook has something to send.');
        }
        BugWebhookDispatcher::dispatch('bug.created', $latestBug->loadMissing('project', 'bugStatus'));
        return back()->with('status', 'Test payload sent — check your channel.');
    }

    private function authorize(Project $project): void
    {
        $user = Auth::user();
        $ws = $project->workspace;
        $role = $ws ? $ws->getMemberRole($user) : null;
        abort_unless(
            $user && (($ws && $ws->isOwner($user)) || in_array($role, ['manager']) || $user->hasRole('superadmin')),
            403
        );
    }
}
