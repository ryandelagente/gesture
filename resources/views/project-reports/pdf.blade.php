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
        .toc ol li { padding: 3px 0; color: #111827; }

        .section { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px 24px; margin-bottom: 16px; page-break-inside: avoid; }
        .section .eyebrow { font-size: 9px; text-transform: uppercase; letter-spacing: .12em; color: #10b981; font-weight: 700; }
        .section h2 { font-size: 18px; margin: 3px 0 12px; color: #111827; }
        .section p { font-size: 11.5px; color: #374151; margin: 4px 0; }

        .kpi-row { display: table; width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 8px; }
        .kpi { display: table-cell; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 14px; width: 33%; }
        .kpi .label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: .06em; }
        .kpi .value { font-size: 22px; font-weight: 700; color: #111827; margin-top: 3px; }
        .kpi .hint { font-size: 9.5px; color: #6b7280; margin-top: 3px; }

        table.kw { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 8px; }
        table.kw th { text-align: left; padding: 6px 8px; background: #f8fafc; color: #6b7280; font-size: 9px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid #e5e7eb; }
        table.kw td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; }
        table.kw .pos { font-weight: 700; color: #111827; }
        table.kw .delta-up { color: #16a34a; font-weight: 600; }
        table.kw .delta-down { color: #dc2626; font-weight: 600; }
        table.kw .delta-flat { color: #6b7280; }
        .kw-bucket { font-size: 10px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin: 12px 0 4px; }

        ul.tasks { list-style: none; padding: 0; margin: 4px 0 0; }
        ul.tasks li { padding: 5px 0; border-bottom: 1px solid #f1f5f9; font-size: 11.5px; }
        ul.tasks .when { float: right; color: #6b7280; font-size: 10px; }
        ul.tasks .check { color: #10b981; font-weight: 700; margin-right: 6px; }
        ul.tasks .open { color: #2563eb; font-weight: 700; margin-right: 6px; }

        .footer-banner { background: #10b981; color: #fff; padding: 22px 24px; border-radius: 8px; text-align: center; margin-top: 16px; }
        .footer-banner h3 { font-size: 18px; margin: 0 0 4px; }
        .footer-banner p { font-size: 11px; margin: 0; opacity: .92; }
    </style>
</head>
<body>

<div class="cover">
    <div class="badge">Prepared with your business' success in mind</div>
    <h1>{{ $project->title }}</h1>
    <div class="sub">Digital monthly report for <strong>{{ strtoupper($start->format('F Y')) }}</strong></div>
    <div class="meta">
        @if ($project->live_url){{ parse_url($project->live_url, PHP_URL_HOST) }} · @endif
        Service: {{ $project->description ?? 'Web Development' }}
    </div>
</div>

<div class="toc">
    <h3>Overview</h3>
    <ol>
        <li>Website Performance</li>
        <li>Leads</li>
        <li>SEO Rankings &amp; Progress</li>
        <li>Google My Business Performance</li>
        <li>AI Search Visibility</li>
        <li>Work We've Done</li>
        <li>Work Ahead for Next Month</li>
    </ol>
</div>

{{-- 1. Website performance --}}
<div class="section">
    <div class="eyebrow">1. Website performance</div>
    <h2>Traffic, users &amp; engagement</h2>
    <div class="kpi-row">
        <div class="kpi"><div class="label">Sessions</div><div class="value">{{ rtrim(rtrim(number_format($metrics['sessions'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">GA4</div></div>
        <div class="kpi"><div class="label">Total users</div><div class="value">{{ rtrim(rtrim(number_format($metrics['users'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">GA4</div></div>
        <div class="kpi"><div class="label">Page views</div><div class="value">{{ rtrim(rtrim(number_format($metrics['page_views'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">GA4</div></div>
    </div>
    <p><strong>{{ $project->title }}</strong> accumulated <strong>{{ rtrim(rtrim(number_format($metrics['sessions'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> sessions and <strong>{{ rtrim(rtrim(number_format($metrics['users'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> unique users during {{ $start->format('F Y') }}.</p>
</div>

{{-- 2. Leads --}}
<div class="section">
    <div class="eyebrow">2. Leads</div>
    <h2>{{ rtrim(rtrim(number_format($metrics['leads'] ?? 0, 2), '0'), '.') ?: '0' }} new leads this month</h2>
    <p>Leads are tracked as GA4 conversions (form submissions + phone-click events) and where applicable, call-tracking data.</p>
</div>

{{-- 3. SEO rankings --}}
<div class="section">
    <div class="eyebrow">3. SEO rankings · Progress</div>
    <h2>Keyword performance</h2>
    @php
        $buckets = ['top3' => 'Top 3 ranking keywords', 'progressing' => 'Keywords that are progressing', 'long_tail' => 'Long-tail keywords'];
    @endphp
    @foreach ($buckets as $key => $label)
        <div class="kw-bucket">{{ $label }}</div>
        <table class="kw">
            <thead><tr><th style="width:60%">Keyword</th><th>Current</th><th>Previous</th><th>Change</th></tr></thead>
            <tbody>
                @forelse ($rankings[$key] ?? [] as $kw)
                    <tr>
                        <td>{{ $kw->keyword }}</td>
                        <td class="pos">{{ $kw->position ?? '—' }}</td>
                        <td>{{ $kw->previous_position ?? '—' }}</td>
                        <td>
                            @if ($kw->position && $kw->previous_position)
                                @php $delta = $kw->previous_position - $kw->position; @endphp
                                @if ($delta > 0) <span class="delta-up">▲ {{ $delta }}</span>
                                @elseif ($delta < 0) <span class="delta-down">▼ {{ abs($delta) }}</span>
                                @else <span class="delta-flat">— flat</span> @endif
                            @else <span class="delta-flat">—</span> @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:#6b7280;font-style:italic;padding:14px">No keywords recorded</td></tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</div>

{{-- 4. GMB --}}
<div class="section">
    <div class="eyebrow">4. Google My Business · Performance</div>
    <h2>How your profile is performing</h2>
    <div class="kpi-row">
        <div class="kpi"><div class="label">Impressions</div><div class="value">{{ rtrim(rtrim(number_format($metrics['gmb_impressions'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">Search + Maps</div></div>
        <div class="kpi"><div class="label">Website clicks</div><div class="value">{{ rtrim(rtrim(number_format($metrics['gmb_clicks'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">GBP</div></div>
        <div class="kpi"><div class="label">Phone calls</div><div class="value">{{ rtrim(rtrim(number_format($metrics['gmb_calls'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">GBP</div></div>
    </div>
    <p><strong>{{ rtrim(rtrim(number_format($metrics['gmb_clicks'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> visitors clicked through to your website from your GMB profile and <strong>{{ rtrim(rtrim(number_format($metrics['gmb_calls'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> tapped the call button.</p>
</div>

{{-- 5. AI Search Visibility --}}
<div class="section">
    <div class="eyebrow">5. AI Search Visibility</div>
    <h2>How your brand appears in AI-powered search</h2>
    <p>AI tools (ChatGPT, Perplexity, Google AI Overviews, Copilot) now answer many search queries directly. This section tracks how often your site is cited and how much traffic those tools refer.</p>
    <div class="kpi-row">
        <div class="kpi"><div class="label">AI referral sessions</div><div class="value">{{ rtrim(rtrim(number_format($metrics['ai_referral_sessions'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">GA4</div></div>
        <div class="kpi"><div class="label">AI referral users</div><div class="value">{{ rtrim(rtrim(number_format($metrics['ai_referral_users'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">GA4</div></div>
        <div class="kpi"><div class="label">AI Overview appearances</div><div class="value">{{ rtrim(rtrim(number_format($metrics['ai_overview_appearances'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">Manual</div></div>
    </div>
    <div class="kpi-row" style="margin-top:8px">
        <div class="kpi" style="width:100%"><div class="label">Brand mentions in AI answers</div><div class="value">{{ rtrim(rtrim(number_format($metrics['ai_mentions'] ?? 0, 2), '0'), '.') ?: '—' }}</div><div class="hint">Tracked queries · manual</div></div>
    </div>
    <p><strong>{{ rtrim(rtrim(number_format($metrics['ai_referral_sessions'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> sessions came from AI search tools, and your brand was cited <strong>{{ rtrim(rtrim(number_format($metrics['ai_mentions'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> times in tracked AI answers during {{ $start->format('F Y') }}.</p>
</div>

{{-- 6. Work We've Done --}}
<div class="section">
    <div class="eyebrow">6. Work we've done</div>
    <h2>Completed in {{ $start->format('F Y') }}</h2>
    @if ($doneTasks->isEmpty())
        <p style="color:#6b7280;font-style:italic">No tasks were marked Done in this period.</p>
    @else
        <ul class="tasks">
            @foreach ($doneTasks as $t)
                <li><span class="check">✓</span>{{ $t->title }}<span class="when">{{ $t->updated_at->format('M j') }}</span></li>
            @endforeach
        </ul>
    @endif
</div>

{{-- 7. Work Ahead --}}
<div class="section">
    <div class="eyebrow">7. Work ahead</div>
    <h2>Planned for the next month</h2>
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
