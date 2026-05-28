<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\BugWidgetKey;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BugWidgetKeyController extends Controller
{
    public function index(Project $project)
    {
        $this->authorizeAccess($project);
        $keys = $project->bugWidgetKeys()->latest()->get();
        $routes = \App\Models\BugWidgetRoute::where('project_id', $project->id)
            ->with('assignee')
            ->orderBy('sort_order')
            ->get();
        $webhooks = \App\Models\ProjectWebhook::where('project_id', $project->id)->latest()->get();
        $publicBoards = \App\Models\ProjectPublicBoard::where('project_id', $project->id)->latest()->get();
        $slaPolicies = [];
        foreach (['low', 'medium', 'high', 'critical'] as $p) {
            $slaPolicies[$p] = \App\Models\ProjectSlaPolicy::lookup($project->id, $p);
        }
        $members = \App\Models\User::whereHas('workspaces', function ($q) use ($project) {
            $q->where('workspace_id', $project->workspace_id)->where('status', 'active');
        })->get(['id', 'name', 'email']);
        $endpoint = url('/api/widget/feedback');
        $scriptUrl = url('/widget.js');
        return view('bug-widget-keys.index', compact('project', 'keys', 'routes', 'webhooks', 'publicBoards', 'slaPolicies', 'members', 'endpoint', 'scriptUrl'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeAccess($project);
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'allowed_origins'  => 'nullable|string|max:2000',
            'brand_color'      => 'nullable|string|max:16',
            'brand_logo_url'   => 'nullable|url|max:512',
            'button_label'     => 'nullable|string|max:60',
            'welcome_text'     => 'nullable|string|max:280',
        ]);

        $origins = collect(explode("\n", $data['allowed_origins'] ?? ''))
            ->map(fn($v) => trim($v))
            ->filter()
            ->values()
            ->all();

        BugWidgetKey::create([
            'project_id'      => $project->id,
            'created_by'      => Auth::id(),
            'name'            => $data['name'],
            'allowed_origins' => $origins,
            'is_enabled'      => true,
            'brand_color'     => $data['brand_color'] ?? null,
            'brand_logo_url'  => $data['brand_logo_url'] ?? null,
            'button_label'    => $data['button_label'] ?? null,
            'welcome_text'    => $data['welcome_text'] ?? null,
        ]);

        return back()->with('status', 'Widget key created.');
    }

    public function destroy(Project $project, BugWidgetKey $key)
    {
        $this->authorizeAccess($project);
        abort_if($key->project_id !== $project->id, 404);
        $key->delete();
        return back()->with('status', 'Widget key deleted.');
    }

    public function toggle(Project $project, BugWidgetKey $key)
    {
        $this->authorizeAccess($project);
        abort_if($key->project_id !== $project->id, 404);
        $key->is_enabled = !$key->is_enabled;
        $key->save();
        return back()->with('status', $key->is_enabled ? 'Enabled.' : 'Disabled.');
    }

    public function bugDetail(Bug $bug)
    {
        $user = Auth::user();
        abort_unless($user && $bug->canBeViewedBy($user), 403);
        $bug->load(['project.workspace', 'bugStatus', 'reportedBy', 'tags']);
        return view('bug-widget-keys.bug-detail', compact('bug'));
    }

    public function applyAiSuggestions(Bug $bug)
    {
        $user = Auth::user();
        abort_unless($user && $bug->canBeUpdatedBy($user), 403);
        $sug = $bug->ai_suggestions ?? [];
        if (!empty($sug['priority'])) $bug->priority = $sug['priority'];
        if (!empty($sug['severity'])) $bug->severity = $sug['severity'];
        if (!empty($sug['summary']))  $bug->title    = $sug['summary'];
        $bug->save();

        // Attach AI-suggested tags (creates ones that don't exist for this workspace)
        if (!empty($sug['suggested_tags']) && is_array($sug['suggested_tags'])) {
            $wsId = $bug->project->workspace_id;
            $tagIds = [];
            foreach ($sug['suggested_tags'] as $name) {
                $tag = \App\Models\BugTag::firstOrCreate(
                    ['workspace_id' => $wsId, 'name' => $name],
                    ['color' => '#6366f1', 'created_by' => $user->id]
                );
                $tagIds[] = $tag->id;
            }
            $bug->tags()->syncWithoutDetaching($tagIds);
        }

        return back()->with('status', 'AI suggestions applied.');
    }

    private function authorizeAccess(Project $project): void
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $ws = $project->workspace;
        $role = $ws ? $ws->getMemberRole($user) : null;
        abort_unless(
            ($ws && $ws->isOwner($user)) || in_array($role, ['manager']) || $user->hasRole('superadmin'),
            403
        );
    }
}
