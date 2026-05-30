<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Monthly Reports — Gesture</title>
    <style>
        :root{--c:#10b981;--c2:#111827;--bg:#f8fafc;--line:#e5e7eb;--muted:#6b7280;--ink:#111827}
        *{box-sizing:border-box}
        body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:var(--bg);color:var(--ink);line-height:1.5}
        header{background:#fff;border-bottom:1px solid var(--line);padding:14px 24px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
        header h1{margin:0;font-size:18px}
        header a{color:var(--c);text-decoration:none;font-size:14px}
        .wrap{max-width:1280px;margin:24px auto;padding:0 16px}
        .toolbar{background:#fff;border:1px solid var(--line);border-radius:8px;padding:14px 16px;margin-bottom:18px;display:flex;align-items:center;gap:14px;flex-wrap:wrap}
        .toolbar input{padding:8px 12px;border:1px solid var(--line);border-radius:6px;font-size:14px;min-width:260px}
        .toolbar button{padding:8px 16px;background:var(--c2);color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer}
        .toolbar .count{font-size:13px;color:var(--muted)}
        table{width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--line);border-radius:8px;overflow:hidden}
        th{text-align:left;padding:10px 14px;background:#f8fafc;border-bottom:1px solid var(--line);font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em}
        td{padding:12px 14px;border-bottom:1px solid var(--line);font-size:14px;vertical-align:middle}
        tr:last-child td{border-bottom:none}
        tr:hover{background:#f8fafc}
        .title{font-weight:600;color:var(--c2)}
        .pill{display:inline-block;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600}
        .pill-seo{background:#dbeafe;color:#1e3a8a}
        .pill-webdev{background:#dcfce7;color:#166534}
        .pill-ads{background:#fef3c7;color:#92400e}
        .pill-default{background:#f3f4f6;color:#6b7280}
        .status-pill{padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600}
        .status-active{background:#dcfce7;color:#166534}
        .status-planning{background:#dbeafe;color:#1e3a8a}
        .status-completed{background:#f3f4f6;color:#6b7280}
        .status-on_hold{background:#fef3c7;color:#92400e}
        .status-cancelled{background:#fee2e2;color:#991b1b}
        .last{font-size:12px;color:var(--muted)}
        .actions a{color:var(--c2);background:#f1f5f9;border:1px solid var(--line);padding:6px 12px;border-radius:5px;font-size:12.5px;text-decoration:none;display:inline-block}
        .actions a:hover{background:var(--c2);color:#fff;border-color:var(--c2)}
        .empty{padding:60px 0;text-align:center;color:var(--muted)}
        .empty h3{color:var(--c2);margin:0 0 6px}
    </style>
</head>
<body>
<header>
    <h1>📊 Monthly Reports — {{ $view === 'agency' ? 'Agency (internal)' : 'Client' }}</h1>
    <a href="{{ url('/dashboard') }}">&larr; Dashboard</a>
</header>

<div class="wrap">
    <div style="display:flex;gap:8px;margin:0 0 14px;font-size:13px">
        <a href="{{ url('/reports') }}" style="padding:6px 12px;border-radius:6px;text-decoration:none;{{ $view === 'client' ? 'background:#10b981;color:#fff' : 'background:#fff;border:1px solid #e5e7eb;color:#374151' }}">👤 Client report</a>
        <a href="{{ url('/reports?view=agency') }}" style="padding:6px 12px;border-radius:6px;text-decoration:none;{{ $view === 'agency' ? 'background:#f59e0b;color:#fff' : 'background:#fff;border:1px solid #e5e7eb;color:#374151' }}">🔒 Agency report</a>
    </div>
    <form class="toolbar" method="GET">
        @if ($view === 'agency')<input type="hidden" name="view" value="agency">@endif
        <input type="search" name="q" placeholder="Search projects…" value="{{ $search }}">
        <button type="submit">Search</button>
        <span class="count">{{ $projects->count() }} project{{ $projects->count() === 1 ? '' : 's' }}</span>
    </form>

    @if ($projects->isEmpty())
        <div class="empty">
            <h3>No projects yet</h3>
            <p>Create a project to see its monthly report here.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Live URL</th>
                    <th>Last sync</th>
                    <th>GA4</th>
                    <th>GSC</th>
                    <th>GBP</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $p)
                    @php
                        $rows = $latest->get($p->id, collect());
                        $latestPeriod = $rows->first()?->period_start;
                        $sources = $rows->pluck('source')->unique();
                    @endphp
                    <tr>
                        <td><span class="title">{{ $p->title }}</span></td>
                        <td>
                            @php
                                $services = array_filter(array_map('trim', explode(',', $p->description ?? '')));
                                if (empty($services)) $services = ['—'];
                            @endphp
                            @foreach ($services as $svc)
                                @php
                                    $low = strtolower($svc);
                                    $cls = match(true) {
                                        str_contains($low, 'seo')      => 'pill-seo',
                                        str_contains($low, 'web')      => 'pill-webdev',
                                        str_contains($low, 'ads')      => 'pill-ads',
                                        default                          => 'pill-default',
                                    };
                                @endphp
                                <span class="pill {{ $cls }}" style="margin-right:4px">{{ $svc }}</span>
                            @endforeach
                        </td>
                        <td><span class="status-pill status-{{ $p->status }}">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span></td>
                        <td>
                            @if ($p->live_url)
                                <a href="{{ $p->live_url }}" target="_blank" rel="noopener" style="color:#2563eb;font-size:12.5px">{{ parse_url($p->live_url, PHP_URL_HOST) }}</a>
                            @else
                                <span class="last">—</span>
                            @endif
                        </td>
                        <td><span class="last">{{ $latestPeriod ? \Carbon\Carbon::parse($latestPeriod)->format('M Y') : '—' }}</span></td>
                        <td>{!! $sources->contains('ga4') ? '<span style="color:#16a34a;font-size:16px">✓</span>' : '<span class="last">—</span>' !!}</td>
                        <td>{!! $sources->contains('gsc') ? '<span style="color:#16a34a;font-size:16px">✓</span>' : '<span class="last">—</span>' !!}</td>
                        <td>{!! $sources->contains('gbp') ? '<span style="color:#16a34a;font-size:16px">✓</span>' : '<span class="last">—</span>' !!}</td>
                        <td class="actions">
                            <a href="{{ url('/projects/' . $p->id . ($view === 'agency' ? '/agency-report' : '/reports')) }}">Open report →</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
</body>
</html>
