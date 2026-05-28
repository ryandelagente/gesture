<?php

namespace App\Http\Controllers;

use App\Models\BugTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BugTagController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $tags = BugTag::where('workspace_id', $user->current_workspace_id)->orderBy('name')->get();
        return response()->json($tags);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->current_workspace_id, 403);
        $data = $request->validate([
            'name'  => 'required|string|max:60',
            'color' => 'nullable|string|max:16',
        ]);
        $tag = BugTag::firstOrCreate(
            ['workspace_id' => $user->current_workspace_id, 'name' => $data['name']],
            ['color' => $data['color'] ?? '#6366f1', 'created_by' => $user->id]
        );
        return response()->json($tag, 201);
    }

    public function destroy(BugTag $tag)
    {
        $user = Auth::user();
        abort_unless($user && $tag->workspace_id === $user->current_workspace_id, 403);
        $tag->delete();
        return response()->json(['ok' => true]);
    }

    public function attach(Request $request, $bugId)
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $data = $request->validate(['tag_ids' => 'required|array', 'tag_ids.*' => 'integer']);
        $bug = \App\Models\Bug::findOrFail($bugId);
        abort_unless($bug->canBeUpdatedBy($user), 403);
        $bug->tags()->sync($data['tag_ids']);
        return response()->json(['ok' => true, 'tags' => $bug->tags()->get()]);
    }
}
