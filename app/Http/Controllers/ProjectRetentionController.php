<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectRetentionController extends Controller
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
            'bug_retention_days'    => 'nullable|integer|min:0|max:3650',
            'retention_widget_only' => 'nullable|boolean',
        ]);

        $project->forceFill([
            'bug_retention_days'    => $data['bug_retention_days'] ?: null,
            'retention_widget_only' => (bool) ($data['retention_widget_only'] ?? false),
        ])->save();

        return back()->with('status', 'Retention policy saved.');
    }
}
