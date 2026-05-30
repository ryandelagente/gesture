<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tasks Overview — {{ ucfirst($category) }}</title>
    <style>
        body { font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:#f8fafc; color:#111827; margin:0; padding:24px 32px; }
        .wrap { max-width: 1200px; margin: 0 auto; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        .sub { color:#6b7280; font-size:13px; margin-bottom:18px; }
        .tabs { display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
        .tab { padding:8px 14px; background:#fff; border:1px solid #e5e7eb; border-radius:999px; text-decoration:none; color:#374151; font-size:13px; font-weight:500; }
        .tab.active { background:#10b981; border-color:#10b981; color:#fff; }
        .tab .n { display:inline-block; margin-left:6px; background:rgba(0,0,0,.08); padding:0 7px; border-radius:999px; font-size:11px; }
        .tab.active .n { background:rgba(255,255,255,.25); }
        table { width:100%; border-collapse:collapse; background:#fff; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden; font-size:13px; }
        th, td { padding:10px 12px; text-align:left; border-bottom:1px solid #f1f5f9; }
        th { background:#f8fafc; color:#6b7280; font-size:11px; text-transform:uppercase; letter-spacing:.05em; }
        tr:last-child td { border-bottom:none; }
        .badge { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; }
        .b-web     { background:#dbeafe; color:#1e40af; }
        .b-content { background:#fef3c7; color:#92400e; }
        .b-general { background:#e5e7eb; color:#374151; }
        .b-low      { background:#f1f5f9; color:#475569; }
        .b-medium   { background:#fef3c7; color:#92400e; }
        .b-high     { background:#fee2e2; color:#991b1b; }
        .b-critical { background:#fecaca; color:#7f1d1d; }
        .muted { color:#9ca3af; font-size:12px; }
        a.proj { color:#2563eb; text-decoration:none; }
        a.proj:hover { text-decoration:underline; }
        .empty { text-align:center; padding:36px; color:#9ca3af; font-style:italic; }
        a.back { font-size:13px; color:#2563eb; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">
    <a class="back" href="{{ url('/dashboard') }}">← Back to dashboard</a>
    <h1 style="margin-top:8px">Tasks Overview</h1>
    <div class="sub">All tasks across <strong>{{ $workspace->name }}</strong> — filter by type.</div>

    @php
        $tabs = [
            'content' => 'Content tasks',
            'web'     => 'Web tasks',
            'general' => 'General',
            'all'     => 'All',
        ];
    @endphp
    <div class="tabs">
        @foreach ($tabs as $key => $label)
            <a class="tab {{ $category === $key ? 'active' : '' }}"
               href="{{ url('/tasks-overview?category=' . $key) }}">
                {{ $label }}<span class="n">{{ $key === 'all' ? $counts->sum() : ($counts[$key] ?? 0) }}</span>
            </a>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30%">Task</th>
                <th>Project</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Stage</th>
                <th>Assignee</th>
                <th>Due</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tasks as $t)
                <tr>
                    <td><strong>{{ $t->title }}</strong></td>
                    <td><a class="proj" href="{{ url('/projects/' . $t->project_id) }}">{{ $t->project?->title }}</a></td>
                    <td><span class="badge b-{{ $t->category ?: 'general' }}">{{ ucfirst($t->category ?: 'general') }}</span></td>
                    <td><span class="badge b-{{ $t->priority }}">{{ ucfirst($t->priority) }}</span></td>
                    <td>{{ $t->taskStage?->name ?? '—' }}</td>
                    <td>{{ $t->assignedTo?->name ?? '—' }}</td>
                    <td>{{ $t->end_date ? $t->end_date->format('M j') : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">No tasks in this category.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p class="muted" style="margin-top:8px">Showing up to 500 most-recent tasks by due date.</p>
</div>
</body>
</html>
