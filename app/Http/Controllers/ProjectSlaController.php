<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectSlaPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectSlaController extends Controller
{
    public function save(Request $request, Project $project)
    {
        $user = Auth::user();
        $ws = $project->workspace;
        $role = $ws ? $ws->getMemberRole($user) : null;
        abort_unless(
            $user && (($ws && $ws->isOwner($user)) || in_array($role, ['manager']) || $user->hasRole('superadmin')),
            403
        );

        $data = $request->validate([
            'policies'                    => 'required|array',
            'policies.*.priority'         => 'required|in:low,medium,high,critical',
            'policies.*.respond_hours'    => 'required|integer|min:1|max:8760',
            'policies.*.resolve_hours'    => 'required|integer|min:1|max:8760',
        ]);

        foreach ($data['policies'] as $row) {
            ProjectSlaPolicy::updateOrCreate(
                ['project_id' => $project->id, 'priority' => $row['priority']],
                ['respond_hours' => $row['respond_hours'], 'resolve_hours' => $row['resolve_hours']]
            );
        }

        return back()->with('status', 'SLA policies saved.');
    }
}
