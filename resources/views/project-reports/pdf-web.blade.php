<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $project->title }} — {{ $start->format('F Y') }}</title>
    <style>
        @page { margin: 18mm 14mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111827; line-height: 1.5; margin: 0; }
        h1, h2, h3 { margin: 0; }

        .cover { background: #111827; color: #fff; padding: 30px 28px; border-radius: 8px; margin-bottom: 22px; }
        .cover .badge { font-size: 9px; letter-spacing: 0.15em; color: #10b981; text-transform: uppercase; font-weight: 700; margin-bottom: 6px; }
        .cover h1 { font-size: 26px; margin: 4px 0 6px; }
        .cover .sub { font-size: 12px; opacity: .85; }
        .cover .meta { margin-top: 14px; font-size: 11px; opacity: .7; }

        .toc { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px 18px; margin-bottom: 22px; }
        .toc h3 { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 8px; }
        .toc ol { margin: 0; padding-left: 24px; font-size: 12px; }
        .toc ol li { padding: 3px 0; }

        .section { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px 24px; margin-bottom: 16px; page-break-inside: avoid; }
        .section .eyebrow { font-size: 9px; text-transform: uppercase; letter-spacing: .12em; color: #10b981; font-weight: 700; }
        .section h2 { font-size: 18px; margin: 3px 0 12px; color: #111827; }
        .section p { font-size: 11.5px; color: #374151; margin: 4px 0; }

        .kpi-row { display: table; width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 8px; }
        .kpi { display: table-cell; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 14px; width: 25%; }
        .kpi .label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: .06em; }
        .kpi .value { font-size: 22px; font-weight: 700; color: #111827; margin-top: 3px; }
        .kpi .hint  { font-size: 9.5px; color: #6b7280; margin-top: 3px; }

        ul.tasks { list-style: none; padding: 0; margin: 6px 0 0; }
        ul.tasks li { padding: 5px 0; border-bottom: 1px solid #f1f5f9; font-size: 11.5px; }
        ul.tasks .when { float: right; color: #6b7280; font-size: 10px; }
        ul.tasks .check { color: #10b981; font-weight: 700; margin-right: 6px; }
        ul.tasks .open  { color: #2563eb; font-weight: 700; margin-right: 6px; }
        ul.tasks .url { color: #2563eb; font-size: 9.5px; margin-left: 6px; }

        .footer-banner { background: #10b981; color: #fff; padding: 22px 24px; border-radius: 8px; text-align: center; margin-top: 16px; }
        .footer-banner h3 { font-size: 18px; margin: 0 0 4px; }
        .footer-banner p { font-size: 11px; margin: 0; opacity: .92; }
    </style>
</head>
<body>

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

<div class="cover">
    <div class="badge">Internal agency report</div>
    <h1>{{ $project->title }}</h1>
    <div class="sub">Digital delivery report for <strong>{{ strtoupper($start->format('F Y')) }}</strong></div>
    <div class="meta">
        @if ($project->live_url){{ parse_url($project->live_url, PHP_URL_HOST) }} · @endif
        Service: {{ $project->description ?? 'Web Development' }}
    </div>
</div>

<div class="toc">
    <h3>Overview</h3>
    <ol>
        <li>Project snapshot</li>
        <li>Content published</li>
        <li>Web work delivered</li>
        @if ($otherDone->isNotEmpty())<li>Other work delivered</li>@endif
        <li>Work ahead for next month</li>
    </ol>
</div>

{{-- 1. SNAPSHOT --}}
<div class="section">
    <div class="eyebrow">1. Project snapshot</div>
    <h2>Status &amp; activity this month</h2>
    <div class="kpi-row">
        <div class="kpi"><div class="label">Progress</div><div class="value">{{ (int) ($project->progress ?? 0) }}%</div><div class="hint">overall</div></div>
        <div class="kpi"><div class="label">Tasks delivered</div><div class="value">{{ $doneTasks->count() }}</div><div class="hint">{{ $start->format('M') }}</div></div>
        <div class="kpi"><div class="label">Content published</div><div class="value">{{ $contentDone->count() }}</div><div class="hint">blog/pages</div></div>
        <div class="kpi"><div class="label">Open tasks</div><div class="value">{{ $upcomingTasks->count() }}</div><div class="hint">scheduled</div></div>
    </div>
    @if ($project->deadline)
        <p>Project deadline: <strong>{{ $project->deadline->format('M j, Y') }}</strong>.</p>
    @endif
</div>

{{-- 2. CONTENT PUBLISHED --}}
<div class="section">
    <div class="eyebrow">2. Content published</div>
    <h2>Blog posts &amp; content pages this month</h2>
    @if ($contentDone->isEmpty())
        <p style="color:#6b7280;font-style:italic">No content pieces were marked Done in this period.</p>
    @else
        <ul class="tasks">
            @foreach ($contentDone as $t)
                @php $url = $extractUrl($t->description); @endphp
                <li>
                    <span class="check">✓</span>{{ $t->title }}
                    @if ($url) <span class="url">{{ $url }}</span> @endif
                    <span class="when">{{ $t->updated_at->format('M j') }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>

{{-- 3. WEB WORK --}}
<div class="section">
    <div class="eyebrow">3. Web work delivered</div>
    <h2>Build &amp; design tasks completed</h2>
    @if ($webDone->isEmpty())
        <p style="color:#6b7280;font-style:italic">No web tasks were completed in this period.</p>
    @else
        <ul class="tasks">
            @foreach ($webDone as $t)
                <li><span class="check">✓</span>{{ $t->title }}<span class="when">{{ $t->updated_at->format('M j') }}</span></li>
            @endforeach
        </ul>
    @endif
</div>

@if ($otherDone->isNotEmpty())
    <div class="section">
        <div class="eyebrow">4. Other work delivered</div>
        <h2>General tasks completed</h2>
        <ul class="tasks">
            @foreach ($otherDone as $t)
                <li><span class="check">✓</span>{{ $t->title }}<span class="when">{{ $t->updated_at->format('M j') }}</span></li>
            @endforeach
        </ul>
    </div>
@endif

{{-- WORK AHEAD --}}
<div class="section">
    <div class="eyebrow">{{ $otherDone->isNotEmpty() ? '5' : '4' }}. Work ahead</div>
    <h2>Planned for next month</h2>
    @if ($upcomingTasks->isEmpty())
        <p style="color:#6b7280;font-style:italic">No upcoming tasks scheduled.</p>
    @else
        <ul class="tasks">
            @foreach ($upcomingTasks as $t)
                <li><span class="open">○</span>{{ $t->title }}<span class="when">{{ $t->end_date ? 'due ' . $t->end_date->format('M j') : '—' }}</span></li>
            @endforeach
        </ul>
    @endif
</div>

<div class="footer-banner">
    <h3>Thank you for your business</h3>
    <p>Let us know if you need our help.</p>
    @if ($project->business_phone)<p style="margin-top:6px"><strong>{{ $project->business_phone }}</strong></p>@endif
</div>

</body>
</html>
