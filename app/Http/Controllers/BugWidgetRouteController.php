<?php

namespace App\Http\Controllers;

use App\Models\BugWidgetRoute;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BugWidgetRouteController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize($project);
        $data = $request->validate([
            'url_pattern'       => 'required|string|max:512',
            'assignee_id'       => 'nullable|integer|exists:users,id',
            'priority_override' => 'nullable|in:low,medium,high,critical',
            'sort_order'        => 'nullable|integer|min:0|max:1000',
        ]);
        $data['project_id'] = $project->id;
        $data['is_enabled'] = true;
        BugWidgetRoute::create($data);
        return back()->with('status', 'Routing rule added.');
    }

    public function destroy(Project $project, BugWidgetRoute $route)
    {
        $this->authorize($project);
        abort_if($route->project_id !== $project->id, 404);
        $route->delete();
        return back()->with('status', 'Routing rule removed.');
    }

    public function toggle(Project $project, BugWidgetRoute $route)
    {
        $this->authorize($project);
        abort_if($route->project_id !== $project->id, 404);
        $route->is_enabled = !$route->is_enabled;
        $route->save();
        return back()->with('status', $route->is_enabled ? 'Enabled.' : 'Disabled.');
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
