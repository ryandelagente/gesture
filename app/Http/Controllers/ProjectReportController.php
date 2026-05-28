<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectKeywordRanking;
use App\Models\ProjectMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ProjectReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $ws = $user->currentWorkspace;
        abort_unless($ws && $ws->is_agency_mode, 404, 'Reports are only available in agency-mode workspaces.');

        $search = trim((string) $request->get('q', ''));

        $projects = Project::where('workspace_id', $ws->id)
            ->when($search, fn ($q) => $q->where('title', 'like', '%' . $search . '%'))
            ->orderBy('title')
            ->get();

        // For each project, find its most recent metric snapshot so we can show
        // a quick preview/sync status next to each row.
        $latest = \App\Models\ProjectMetric::whereIn('project_id', $projects->pluck('id'))
            ->orderBy('period_start', 'desc')
            ->get()
            ->groupBy('project_id');

        return view('project-reports.index', compact('projects', 'latest', 'search'));
    }

    public function show(Request $request, Project $project)
    {
        $this->authorizeAccess($project);

        // Period defaults to the previous full month
        $period = $request->get('month');
        $start  = $period ? Carbon::parse($period)->startOfMonth() : now()->subMonth()->startOfMonth();
        $end    = (clone $start)->endOfMonth();

        $metrics = ProjectMetric::where('project_id', $project->id)
            ->where('period_start', $start->toDateString())
            ->pluck('metric_value', 'metric_key');

        $rankings = ProjectKeywordRanking::where('project_id', $project->id)
            ->where('period_start', $start->toDateString())
            ->orderBy('position')
            ->get()
            ->groupBy('bucket');

        // Auto-aggregate tasks for "Work We've Done" / "Work Ahead"
        $doneTasks = \App\Models\Task::where('project_id', $project->id)
            ->whereBetween('updated_at', [$start, $end])
            ->whereHas('taskStage', fn($q) => $q->where('name', 'Done'))
            ->orderBy('updated_at')
            ->get();

        $upcomingTasks = \App\Models\Task::where('project_id', $project->id)
            ->whereHas('taskStage', fn($q) => $q->whereNotIn('name', ['Done']))
            ->where(function ($q) use ($end) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $end);
            })
            ->orderBy('end_date')
            ->take(10)
            ->get();

        // Convenient prev/next month nav
        $prevMonth = (clone $start)->subMonth()->format('Y-m');
        $nextMonth = (clone $start)->addMonth()->format('Y-m');

        return view('project-reports.show', compact(
            'project', 'start', 'end', 'metrics', 'rankings',
            'doneTasks', 'upcomingTasks', 'prevMonth', 'nextMonth'
        ));
    }

    public function downloadPdf(Request $request, Project $project)
    {
        $this->authorizeAccess($project);

        $period = $request->get('month');
        $start  = $period ? Carbon::parse($period)->startOfMonth() : now()->subMonth()->startOfMonth();
        $end    = (clone $start)->endOfMonth();

        $metrics = ProjectMetric::where('project_id', $project->id)
            ->where('period_start', $start->toDateString())
            ->pluck('metric_value', 'metric_key');

        $rankings = ProjectKeywordRanking::where('project_id', $project->id)
            ->where('period_start', $start->toDateString())
            ->orderBy('position')
            ->get()
            ->groupBy('bucket');

        $doneTasks = \App\Models\Task::where('project_id', $project->id)
            ->whereBetween('updated_at', [$start, $end])
            ->whereHas('taskStage', fn($q) => $q->where('name', 'Done'))
            ->orderBy('updated_at')
            ->get();

        $upcomingTasks = \App\Models\Task::where('project_id', $project->id)
            ->whereHas('taskStage', fn($q) => $q->whereNotIn('name', ['Done']))
            ->where(function ($q) use ($end) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $end);
            })
            ->orderBy('end_date')
            ->take(10)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('project-reports.pdf', compact(
            'project', 'start', 'end', 'metrics', 'rankings', 'doneTasks', 'upcomingTasks'
        ))->setPaper('a4');

        $safeTitle = preg_replace('/[^A-Za-z0-9_\- ]/', '', $project->title);
        $filename  = trim($safeTitle) . ' - ' . $start->format('Y-m') . '.pdf';

        return $pdf->download($filename);
    }

    public function syncGbp(Request $request, Project $project)
    {
        $this->authorizeAccess($project);
        if (empty($project->gbp_location_id)) {
            return back()->withErrors(['gbp' => 'Set the GBP location ID on the project before syncing.']);
        }
        $month = $request->get('month') ? Carbon::parse($request->get('month'))->startOfMonth() : now()->subMonth()->startOfMonth();
        $result = \App\Services\GbpSyncService::syncProject($project, $month);
        if ($result === null) {
            return back()->withErrors(['gbp' => 'GBP sync failed. Check storage/logs/laravel.log for details.']);
        }
        return back()->with('status', "GBP sync for {$month->format('F Y')} complete — {$result['gmb_clicks']} website clicks, {$result['gmb_calls']} calls, {$result['gmb_directions']} directions, {$result['gmb_impressions']} impressions.");
    }

    public function syncGsc(Request $request, Project $project)
    {
        $this->authorizeAccess($project);
        if (empty($project->gsc_site_url)) {
            return back()->withErrors(['gsc' => 'Set the GSC site URL on the project before syncing.']);
        }
        $month = $request->get('month') ? Carbon::parse($request->get('month'))->startOfMonth() : now()->subMonth()->startOfMonth();
        $result = \App\Services\GscSyncService::syncProject($project, $month);
        if ($result === null) {
            return back()->withErrors(['gsc' => 'GSC sync failed. Check storage/logs/laravel.log for details.']);
        }
        return back()->with('status', "GSC sync for {$month->format('F Y')} complete — {$result['keywords_synced']} keywords, {$result['clicks']} clicks, {$result['impressions']} impressions.");
    }

    public function syncGa4(Request $request, Project $project)
    {
        $this->authorizeAccess($project);
        if (empty($project->ga4_property_id)) {
            return back()->withErrors(['ga4' => 'Set the GA4 Property ID on the project before syncing.']);
        }
        $month = $request->get('month') ? Carbon::parse($request->get('month'))->startOfMonth() : now()->subMonth()->startOfMonth();
        $result = \App\Services\Ga4SyncService::syncProject($project, $month);
        if ($result === null) {
            return back()->withErrors(['ga4' => 'GA4 sync failed. Check storage/logs/laravel.log for details.']);
        }
        $summary = collect($result)->map(fn ($v, $k) => "$k=$v")->implode(', ');
        return back()->with('status', "GA4 sync for {$month->format('F Y')} complete — $summary");
    }

    public function saveMetrics(Request $request, Project $project)
    {
        $this->authorizeAccess($project);

        $data = $request->validate([
            'period'                => 'required|date_format:Y-m',
            'metrics'               => 'array',
            'metrics.*'             => 'nullable|numeric|min:0',
            'keywords'              => 'array',
            'keywords.*.keyword'    => 'nullable|string|max:255',
            'keywords.*.position'   => 'nullable|integer|min:1|max:100',
            'keywords.*.previous'   => 'nullable|integer|min:1|max:100',
            'keywords.*.bucket'     => 'nullable|in:top3,progressing,long_tail',
        ]);

        $start = Carbon::parse($data['period'])->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        foreach (($data['metrics'] ?? []) as $key => $value) {
            if ($value === null || $value === '') continue;
            ProjectMetric::updateOrCreate(
                ['project_id' => $project->id, 'period_start' => $start, 'metric_key' => $key],
                ['period_end' => $end, 'metric_value' => $value, 'source' => 'manual']
            );
        }

        // Replace keyword rankings for this month
        ProjectKeywordRanking::where('project_id', $project->id)
            ->where('period_start', $start->toDateString())
            ->delete();
        foreach (($data['keywords'] ?? []) as $row) {
            if (empty($row['keyword'])) continue;
            ProjectKeywordRanking::create([
                'project_id'        => $project->id,
                'period_start'      => $start,
                'keyword'           => $row['keyword'],
                'position'          => $row['position'] ?? null,
                'previous_position' => $row['previous'] ?? null,
                'bucket'            => $row['bucket'] ?? 'progressing',
            ]);
        }

        return back()->with('status', 'Report data saved.');
    }

    private function authorizeAccess(Project $project): void
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $ws   = $project->workspace;
        // Reports are an agency-mode feature only
        abort_unless($ws && $ws->is_agency_mode, 404, 'Reports are only available in agency-mode workspaces.');
        $role = $ws->getMemberRole($user);
        abort_unless(
            $ws->isOwner($user) || in_array($role, ['manager', 'member', 'client']) || $user->hasRole('superadmin'),
            403
        );
    }
}
