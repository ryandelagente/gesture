<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Bug #{{ $bug->id }} — widget capture</title>
    <style>
        :root { --c:#2563eb; --bg:#f8fafc; --line:#e5e7eb; --muted:#6b7280; }
        *{box-sizing:border-box}
        body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:var(--bg);color:#111827}
        header{background:#fff;border-bottom:1px solid var(--line);padding:14px 24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
        header h1{margin:0;font-size:18px}
        header a{color:var(--c);text-decoration:none;font-size:14px}
        .wrap{max-width:1100px;margin:24px auto;padding:0 16px}
        .grid{display:grid;grid-template-columns:1fr 320px;gap:18px}
        @media(max-width:880px){.grid{grid-template-columns:1fr}}
        .card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:18px;margin-bottom:18px}
        .card h2{margin:0 0 12px;font-size:15px}
        dl{margin:0;display:grid;grid-template-columns:130px 1fr;gap:6px 14px;font-size:14px}
        dt{color:var(--muted);font-weight:500}
        dd{margin:0;word-break:break-word}
        code{background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:12.5px;font-family:ui-monospace,SFMono-Regular,monospace}
        .screenshot{position:relative;border:1px solid var(--line);border-radius:6px;overflow:hidden;background:#0f172a}
        .screenshot img{display:block;width:100%;height:auto}
        .pin-overlay{position:absolute;width:18px;height:18px;border-radius:50%;background:var(--c);border:3px solid #fff;box-shadow:0 4px 10px rgba(0,0,0,.4);transform:translate(-50%,-50%);pointer-events:none}
        .pill{display:inline-block;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.04em}
        .pill-widget{background:#dbeafe;color:#1e3a8a}
        .pill-internal{background:#f3f4f6;color:#374151}
        .desc{white-space:pre-wrap;font-size:14px;line-height:1.6}
        .empty{color:var(--muted);font-style:italic}
    </style>
</head>
<body>
    <header>
        <h1>Bug #{{ $bug->id }} — {{ $bug->title }}</h1>
        <div>
            @if ($bug->source === 'widget')
                <span class="pill pill-widget">widget capture</span>
            @else
                <span class="pill pill-internal">internal</span>
            @endif
            <a href="{{ url('/bugs') }}" style="margin-left:14px">&larr; All bugs</a>
        </div>
    </header>

    <div class="wrap">
        <div class="grid">
            <div>
                <div class="card">
                    <h2>Screenshot</h2>
                    @if ($bug->screenshot_path)
                        @php
                            $screenshotUrl = asset('storage/' . $bug->screenshot_path);
                            $hasPin = $bug->pin_x !== null && $bug->pin_y !== null && $bug->viewport_w && $bug->viewport_h;
                            $pinLeft = $hasPin ? ($bug->pin_x / $bug->viewport_w) * 100 : null;
                            $pinTop  = $hasPin ? ($bug->pin_y / $bug->viewport_h) * 100 : null;
                        @endphp
                        <div class="screenshot">
                            <img src="{{ $screenshotUrl }}" alt="Screenshot">
                            @if ($hasPin)
                                <div class="pin-overlay" style="left:{{ $pinLeft }}%;top:{{ $pinTop }}%"></div>
                            @endif
                        </div>
                    @else
                        <p class="empty">No screenshot captured.</p>
                    @endif
                </div>

                <div class="card">
                    <h2>Description</h2>
                    <div class="desc">{{ $bug->description ?: 'No description' }}</div>
                </div>

                @if (!empty($bug->ai_suggestions))
                    <div class="card" style="border-color:#a78bfa;background:linear-gradient(135deg,#faf5ff,#fff)">
                        <h2 style="display:flex;align-items:center;gap:8px;color:#6d28d9">✨ AI triage suggestions</h2>
                        <dl style="grid-template-columns:120px 1fr">
                            @if (!empty($bug->ai_suggestions['summary']))
                                <dt>Title</dt><dd><strong>{{ $bug->ai_suggestions['summary'] }}</strong></dd>
                            @endif
                            @if (!empty($bug->ai_suggestions['priority']))
                                <dt>Priority</dt><dd>{{ ucfirst($bug->ai_suggestions['priority']) }}</dd>
                            @endif
                            @if (!empty($bug->ai_suggestions['severity']))
                                <dt>Severity</dt><dd>{{ ucfirst($bug->ai_suggestions['severity']) }}</dd>
                            @endif
                            @if (!empty($bug->ai_suggestions['suggested_tags']))
                                <dt>Tags</dt><dd>
                                    @foreach ($bug->ai_suggestions['suggested_tags'] as $t)
                                        <span style="display:inline-block;padding:2px 8px;border-radius:9999px;font-size:11px;background:#ede9fe;color:#5b21b6;margin:2px 4px 2px 0">{{ $t }}</span>
                                    @endforeach
                                </dd>
                            @endif
                        </dl>
                        <form method="POST" action="{{ url('/bugs/' . $bug->id . '/apply-ai') }}" style="margin-top:10px">
                            @csrf
                            <button type="submit" style="background:#6d28d9;color:#fff;border:none;padding:8px 14px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500">Apply suggestions</button>
                        </form>
                        @if (!empty($bug->ai_suggestions['generated_at']))
                            <p style="margin:8px 0 0;font-size:11px;color:#6b7280">Generated by {{ $bug->ai_suggestions['model'] ?? 'AI' }} · {{ \Carbon\Carbon::parse($bug->ai_suggestions['generated_at'])->diffForHumans() }}</p>
                        @endif
                    </div>
                @endif

                @if ($bug->video_path)
                    <div class="card">
                        <h2>Recording @if ($bug->video_duration_s) <span style="font-weight:400;font-size:12px;color:#6b7280">({{ $bug->video_duration_s }}s)</span> @endif</h2>
                        <video controls style="width:100%;border-radius:6px;border:1px solid var(--line);background:#0f172a" src="{{ asset('storage/' . $bug->video_path) }}"></video>
                    </div>
                @endif

                @if (!empty($bug->js_errors))
                    <div class="card">
                        <h2 style="color:#dc2626">⚠ JavaScript errors captured ({{ count($bug->js_errors) }})</h2>
                        <div style="font-family:ui-monospace,SFMono-Regular,monospace;font-size:12px">
                            @foreach ($bug->js_errors as $err)
                                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:10px 12px;margin-bottom:8px">
                                    <div style="color:#991b1b;font-weight:600;margin-bottom:4px">{{ $err['message'] ?? '' }}</div>
                                    @if (!empty($err['source']))
                                        <div style="color:#6b7280">{{ $err['source'] }}@if (!empty($err['line'])):{{ $err['line'] }}@if (!empty($err['col'])):{{ $err['col'] }}@endif @endif</div>
                                    @endif
                                    @if (!empty($err['stack']))
                                        <pre style="background:#0f172a;color:#fca5a5;padding:8px;border-radius:4px;margin:6px 0 0;white-space:pre-wrap;font-size:11px">{{ $err['stack'] }}</pre>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (!empty($bug->console_log))
                    <div class="card">
                        <h2>Browser console ({{ count($bug->console_log) }} entries)</h2>
                        <div style="background:#0f172a;color:#e2e8f0;padding:12px;border-radius:6px;font-family:ui-monospace,SFMono-Regular,monospace;font-size:12px;max-height:340px;overflow:auto">
                            @foreach ($bug->console_log as $entry)
                                @php
                                    $colors = ['log'=>'#cbd5e1','info'=>'#7dd3fc','warn'=>'#fbbf24','error'=>'#fca5a5','debug'=>'#a78bfa'];
                                    $color = $colors[$entry['level'] ?? 'log'] ?? '#cbd5e1';
                                @endphp
                                <div style="padding:3px 0;border-bottom:1px solid #1e293b">
                                    <span style="color:{{ $color }};text-transform:uppercase;font-size:10px;font-weight:600;display:inline-block;width:48px">{{ $entry['level'] ?? 'log' }}</span>
                                    <span style="color:#e2e8f0">{{ $entry['message'] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <div class="card">
                    <h2>Reporter</h2>
                    <dl>
                        <dt>Source</dt><dd>{{ $bug->source }}</dd>
                        @if ($bug->guest_name)
                            <dt>Name</dt><dd>{{ $bug->guest_name }}</dd>
                        @endif
                        @if ($bug->guest_email)
                            <dt>Email</dt><dd>{{ $bug->guest_email }}</dd>
                        @endif
                        @if ($bug->reportedBy)
                            <dt>User</dt><dd>{{ $bug->reportedBy->name }}</dd>
                        @endif
                        <dt>Reported</dt><dd>{{ $bug->created_at?->diffForHumans() }}</dd>
                    </dl>
                </div>

                <div class="card">
                    <h2>Triage</h2>
                    <dl>
                        <dt>Status</dt><dd>{{ $bug->bugStatus->name ?? '—' }}</dd>
                        <dt>Priority</dt><dd>{{ $bug->priority }}</dd>
                        <dt>Severity</dt><dd>{{ $bug->severity }}</dd>
                        <dt>Project</dt><dd>{{ $bug->project->title ?? '—' }}</dd>
                        @if ($bug->due_at)
                            @php $overdue = !$bug->resolved_at && $bug->due_at->isPast(); @endphp
                            <dt>SLA due</dt>
                            <dd>
                                {{ $bug->due_at->format('Y-m-d H:i') }}
                                @if ($bug->resolved_at)
                                    <span class="pill" style="background:#dcfce7;color:#166534">resolved {{ $bug->resolved_at->diffForHumans($bug->due_at) }}</span>
                                @elseif ($overdue)
                                    <span class="pill" style="background:#fee2e2;color:#991b1b">⚠ overdue {{ $bug->due_at->diffForHumans() }}</span>
                                @else
                                    <span class="pill" style="background:#dbeafe;color:#1e3a8a">due {{ $bug->due_at->diffForHumans() }}</span>
                                @endif
                            </dd>
                        @endif
                        <dt>Tags</dt>
                        <dd>
                            @forelse ($bug->tags as $tag)
                                <span style="display:inline-block;padding:2px 8px;border-radius:9999px;font-size:11px;background:{{ $tag->color }}22;color:{{ $tag->color }};border:1px solid {{ $tag->color }}55;margin:2px 4px 2px 0">{{ $tag->name }}</span>
                            @empty
                                <span class="empty">—</span>
                            @endforelse
                        </dd>
                    </dl>
                </div>

                <div class="card">
                    <h2>Page</h2>
                    <dl>
                        <dt>URL</dt>
                        <dd>
                            @if ($bug->page_url)
                                <a href="{{ $bug->page_url }}" target="_blank" rel="noopener">{{ $bug->page_url }}</a>
                            @else
                                <span class="empty">—</span>
                            @endif
                        </dd>
                        <dt>Element</dt>
                        <dd>{{ $bug->element_selector ? '' : '' }}{!! $bug->element_selector ? '<code>' . e($bug->element_selector) . '</code>' : '<span class="empty">—</span>' !!}</dd>
                        <dt>Pin</dt>
                        <dd>{{ $bug->pin_x !== null ? $bug->pin_x . ', ' . $bug->pin_y . ' px' : '—' }}</dd>
                        <dt>Viewport</dt>
                        <dd>{{ $bug->viewport_w ? $bug->viewport_w . ' × ' . $bug->viewport_h : '—' }}</dd>
                    </dl>
                </div>

                @if (!empty($bug->perf_metrics))
                    <div class="card">
                        <h2>Performance</h2>
                        <dl>
                            @if (isset($bug->perf_metrics['lcp']))
                                <dt>LCP</dt><dd>{{ number_format($bug->perf_metrics['lcp']) }} ms
                                    @php $lcp = $bug->perf_metrics['lcp']; @endphp
                                    <span class="pill" style="background:{{ $lcp<2500?'#dcfce7':($lcp<4000?'#fef3c7':'#fee2e2') }};color:{{ $lcp<2500?'#166534':($lcp<4000?'#92400e':'#991b1b') }}">{{ $lcp<2500?'good':($lcp<4000?'needs work':'poor') }}</span>
                                </dd>
                            @endif
                            @if (isset($bug->perf_metrics['cls']))
                                <dt>CLS</dt><dd>{{ $bug->perf_metrics['cls'] }}
                                    @php $cls = $bug->perf_metrics['cls']; @endphp
                                    <span class="pill" style="background:{{ $cls<0.1?'#dcfce7':($cls<0.25?'#fef3c7':'#fee2e2') }};color:{{ $cls<0.1?'#166534':($cls<0.25?'#92400e':'#991b1b') }}">{{ $cls<0.1?'good':($cls<0.25?'needs work':'poor') }}</span>
                                </dd>
                            @endif
                            @if (isset($bug->perf_metrics['fid']))
                                <dt>FID</dt><dd>{{ number_format($bug->perf_metrics['fid']) }} ms</dd>
                            @endif
                            @if (isset($bug->perf_metrics['fcp']))
                                <dt>FCP</dt><dd>{{ number_format($bug->perf_metrics['fcp']) }} ms</dd>
                            @endif
                            @if (isset($bug->perf_metrics['ttfb']))
                                <dt>TTFB</dt><dd>{{ number_format($bug->perf_metrics['ttfb']) }} ms</dd>
                            @endif
                            @if (isset($bug->perf_metrics['full_load_ms']))
                                <dt>Page load</dt><dd>{{ number_format($bug->perf_metrics['full_load_ms']) }} ms</dd>
                            @endif
                        </dl>
                    </div>
                @endif

                <div class="card">
                    <h2>Environment</h2>
                    <dl>
                        <dt>Browser</dt><dd>{{ $bug->browser ?? '—' }}</dd>
                        <dt>OS</dt><dd>{{ $bug->os ?? '—' }}</dd>
                        <dt>User-Agent</dt><dd style="font-size:12px;color:var(--muted)">{{ $bug->user_agent ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
