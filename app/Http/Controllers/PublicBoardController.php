<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\Project;
use App\Models\ProjectPublicBoard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicBoardController extends Controller
{
    public function show(string $token)
    {
        $board = ProjectPublicBoard::where('share_token', $token)->where('is_enabled', true)->firstOrFail();
        $board->forceFill(['last_viewed_at' => now()])->save();

        $bugsQuery = Bug::with(['bugStatus', 'tags'])
            ->where('project_id', $board->project_id)
            ->latest();

        if ($board->show_widget_only) {
            $bugsQuery->where('source', 'widget');
        }

        $bugs = $bugsQuery->limit(200)->get();
        $project = Project::find($board->project_id);
        return view('public-board.show', compact('board', 'project', 'bugs'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize($project);
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'show_widget_only' => 'nullable|boolean',
            'show_screenshots' => 'nullable|boolean',
        ]);
        $data['project_id'] = $project->id;
        $data['created_by'] = Auth::id();
        $data['show_widget_only'] = (bool) ($data['show_widget_only'] ?? false);
        $data['show_screenshots'] = (bool) ($data['show_screenshots'] ?? true);
        $data['is_enabled']       = true;
        ProjectPublicBoard::create($data);
        return back()->with('status', 'Public board created.');
    }

    public function destroy(Project $project, ProjectPublicBoard $board)
    {
        $this->authorize($project);
        abort_if($board->project_id !== $project->id, 404);
        $board->delete();
        return back()->with('status', 'Board removed.');
    }

    public function toggle(Project $project, ProjectPublicBoard $board)
    {
        $this->authorize($project);
        abort_if($board->project_id !== $project->id, 404);
        $board->is_enabled = !$board->is_enabled;
        $board->save();
        return back()->with('status', $board->is_enabled ? 'Enabled.' : 'Disabled.');
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
