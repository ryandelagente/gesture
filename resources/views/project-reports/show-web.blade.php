<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $project->title }} — {{ $start->format('F Y') }} report</title>
    <style>
        :root { --c:#10b981; --ink:#111827; --muted:#6b7280; --line:#e5e7eb; --bg:#f8fafc; }
        body { font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:var(--bg); color:var(--ink); margin:0; padding:24px 28px; }
        .wrap { max-width: 1000px; margin: 0 auto; }
        header.app { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
        header.app h1 { font-size:20px; margin:0; }
        .nav a { display:inline-block; padding:7px 14px; background:#fff; border:1px solid var(--line); border-radius:8px; font-size:12.5px; color:#374151; text-decoration:none; margin-left:6px; }
        .nav a.current { background:#111827; color:#fff; border-color:#111827; }
        .flash { background:#ecfdf5; border:1px solid var(--c); color:#065f46; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:14px; }

        .cover { background:#111827; color:#fff; padding:28px 30px; border-radius:10px; margin-bottom:18px; }
        .cover .badge { font-size:9px; letter-spacing:.15em; color:var(--c); text-transform:uppercase; font-weight:700; margin-bottom:6px; }
        .cover h2 { font-size:24px; margin:4px 0 6px; }
        .cover .sub { font-size:12px; opacity:.85; }
        .cover .meta { margin-top:12px; font-size:11px; opacity:.7; }

        section.report { background:#fff; border:1px solid var(--line); border-radius:10px; padding:22px 26px; margin-bottom:16px; }
        section.report h2 { font-size:11px; color:var(--c); text-transform:uppercase; letter-spacing:.12em; margin:0; font-weight:700; }
        section.report h3 { font-size:18px; margin:4px 0 12px; color:var(--ink); }
        section.report .intro { font-size:13px; color:#374151; margin:6px 0 10px; }

        .kpi-grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:10px; margin:8px 0 4px; }
        .kpi { background:var(--bg); border:1px solid var(--line); border-radius:8px; padding:12px 14px; }
        .kpi .label { font-size:9px; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
        .kpi .value { font-size:22px; font-weight:700; color:var(--ink); margin-top:3px; }
        .kpi .hint  { font-size:10px; color:var(--muted); margin-top:2px; }

        ul.tasks { list-style:none; padding:0; margin:6px 0 0; }
        ul.tasks li { display:flex; justify-content:space-between; padding:7px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
        ul.tasks li:last-child { border-bottom:none; }
        ul.tasks .check { color:var(--c); font-weight:700; margin-right:6px; }
        ul.tasks .open  { color:#2563eb; font-weight:700; margin-right:6px; }
        ul.tasks .when  { color:var(--muted); font-size:11px; }
        .sub-h { font-size:13px; color:var(--ink); font-weight:600; margin:16px 0 4px; display:flex; align-items:center; gap:6px; }
        .sub-h .count { display:inline-block; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; padding:1px 8px; border-radius:999px; font-size:11px; font-weight:600; }

        footer.thanks { background:var(--c); color:#fff; padding:22px 24px; border-radius:10px; text-align:center; margin-top:14px; }
        footer.thanks h3 { margin:0 0 4px; font-size:18px; }
        footer.thanks p { margin:0; font-size:12px; opacity:.92; }

        @media print {
            header.app, .nav { display:none; }
            body { background:#fff; }
            section.report { break-inside:avoid; border:none; box-shadow:none; padding:18px 0; }
        }
    </style>
</head>
<body>

<header class="app">
    <div>
        <h1>{{ $project->title }} — Monthly Report</h1>
        <div style="font-size:12px;color:var(--muted)">{{ $start->format('F Y') }} · Web &amp; content delivery</div>
    </div>
    <div class="nav">
        <a href="{{ url('/projects/' . $project->id . '/agency-report?month=' . $prevMonth) }}">← {{ \Carbon\Carbon::parse($prevMonth)->format('M Y') }}</a>
        <a href="#" class="current">{{ $start->format('M Y') }}</a>
        <a href="{{ url('/projects/' . $project->id . '/agency-report?month=' . $nextMonth) }}">{{ \Carbon\Carbon::parse($nextMonth)->format('M Y') }} →</a>
        <a href="{{ url('/projects/' . $project->id . '/agency-report.pdf?month=' . $start->format('Y-m')) }}" title="Download PDF">📄 Download PDF</a>
        <a href="javascript:window.print()" title="Print">🖨 Print</a>
        <a href="{{ url('/projects/' . $project->id . '/reports?month=' . $start->format('Y-m')) }}" title="Client SEO report" style="background:#dbeafe;border-color:#60a5fa;color:#1e40af">👤 Client view</a>
        <a href="{{ url('/projects/' . $project->id) }}">Back to project</a>
    </div>
</header>

<div class="wrap">

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @php
        $contentDone = $doneTasks->where('category', 'content')->values();
        $webDone     = $doneTasks->where('category', 'web')->values();
        $otherDone   = $doneTasks->whereNotIn('category', ['content', 'web'])->values();
        $extractUrl = function ($text) {
            if (!$text) return null;
            if (preg_match('#https?://[^\s<>"\']+#', $text, $m)) return $m[0];
            return null;
        };
    @endphp

    {{-- COVER --}}
    <div class="cover">
        <div class="badge">🔒 Internal agency report</div>
        <h2>{{ $project->title }}</h2>
        <p class="sub">Digital delivery report for <strong>{{ strtoupper($start->format('F Y')) }}</strong></p>
        <div class="meta">
            @if ($project->live_url) {{ parse_url($project->live_url, PHP_URL_HOST) }} · @endif
            Service: {{ $project->description ?? 'Web Development' }}
        </div>
    </div>

    {{-- 1. PROJECT SNAPSHOT --}}
    <section class="report">
        <h2>1. Project snapshot</h2>
        <h3>Status &amp; activity this month</h3>
        <div class="kpi-grid">
            <div class="kpi">
                <div class="label">Progress</div>
                <div class="value">{{ (int) ($project->progress ?? 0) }}%</div>
                <div class="hint">overall completion</div>
            </div>
            <div class="kpi">
                <div class="label">Tasks delivered</div>
                <div class="value">{{ $doneTasks->count() }}</div>
                <div class="hint">marked Done in {{ $start->format('M') }}</div>
            </div>
            <div class="kpi">
                <div class="label">Content published</div>
                <div class="value">{{ $contentDone->count() }}</div>
                <div class="hint">blog posts &amp; pages</div>
            </div>
            <div class="kpi">
                <div class="label">Open tasks</div>
                <div class="value">{{ $upcomingTasks->count() }}</div>
                <div class="hint">scheduled going forward</div>
            </div>
        </div>
        @if ($project->deadline)
            <p class="intro">Project deadline: <strong>{{ $project->deadline->format('M j, Y') }}</strong>.</p>
        @endif
    </section>

    {{-- 2. CONTENT PUBLISHED --}}
    <section class="report">
        <h2>2. Content published</h2>
        <h3>📝 Blog posts &amp; content pages we wrote this month</h3>
        @if ($contentDone->isEmpty())
            <p class="intro" style="color:var(--muted);font-style:italic">No content pieces were marked Done in {{ $start->format('F Y') }} yet.</p>
        @else
            <ul class="tasks">
                @foreach ($contentDone as $t)
                    @php $url = $extractUrl($t->description); @endphp
                    <li>
                        <span>
                            <span class="check">✓</span>&nbsp;{{ $t->title }}
                            @if ($url) <a href="{{ $url }}" target="_blank" rel="noopener" style="font-size:11px;color:#2563eb;margin-left:8px">view ↗</a> @endif
                        </span>
                        <span class="when">{{ $t->updated_at->format('M j') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- 3. WEB WORK DELIVERED --}}
    <section class="report">
        <h2>3. Web work delivered</h2>
        <h3>🛠 Build &amp; design tasks completed</h3>
        @if ($webDone->isEmpty())
            <p class="intro" style="color:var(--muted);font-style:italic">No web tasks were completed in this period.</p>
        @else
            <ul class="tasks">
                @foreach ($webDone as $t)
                    <li>
                        <span><span class="check">✓</span>&nbsp;{{ $t->title }}</span>
                        <span class="when">{{ $t->updated_at->format('M j') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- 4. OTHER WORK --}}
    @if ($otherDone->isNotEmpty())
        <section class="report">
            <h2>4. Other work delivered</h2>
            <h3>🔧 General tasks completed</h3>
            <ul class="tasks">
                @foreach ($otherDone as $t)
                    <li>
                        <span><span class="check">✓</span>&nbsp;{{ $t->title }}</span>
                        <span class="when">{{ $t->updated_at->format('M j') }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 5. WORK AHEAD --}}
    <section class="report">
        <h2>5. Work ahead</h2>
        <h3>Coming up next month</h3>
        @if ($upcomingTasks->isEmpty())
            <p class="intro" style="color:var(--muted);font-style:italic">No upcoming tasks scheduled.</p>
        @else
            <ul class="tasks">
                @foreach ($upcomingTasks as $t)
                    <li>
                        <span><span class="open">○</span>&nbsp;{{ $t->title }}</span>
                        <span class="when">{{ $t->end_date ? 'due ' . $t->end_date->format('M j') : '—' }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <footer class="thanks">
        <h3>Thank you for your business</h3>
        <p>Let us know if you need our help.</p>
        @if ($project->business_phone)<p style="margin-top:4px"><strong>{{ $project->business_phone }}</strong></p>@endif
    </footer>

</div>
</body>
</html>
