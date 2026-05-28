<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Bug Widget Keys — {{ $project->title }}</title>
    <style>
        :root { --c:#2563eb; --bg:#f8fafc; --line:#e5e7eb; --muted:#6b7280; }
        *{box-sizing:border-box}
        body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:var(--bg);color:#111827}
        header{background:#fff;border-bottom:1px solid var(--line);padding:14px 24px;display:flex;justify-content:space-between;align-items:center}
        header h1{margin:0;font-size:18px}
        header a{color:var(--c);text-decoration:none;font-size:14px}
        .wrap{max-width:980px;margin:24px auto;padding:0 16px}
        .card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:20px;margin-bottom:18px}
        .card h2{margin:0 0 14px;font-size:16px}
        .flash{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:14px}
        table{width:100%;border-collapse:collapse}
        th,td{text-align:left;padding:10px 8px;border-bottom:1px solid var(--line);font-size:14px;vertical-align:top}
        th{font-weight:600;color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.04em}
        code{background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:12.5px;font-family:ui-monospace,SFMono-Regular,monospace;word-break:break-all}
        .key-copy{cursor:pointer;background:#f1f5f9;border:1px solid var(--line);padding:6px 10px;border-radius:4px;font-family:ui-monospace,monospace;font-size:12.5px;word-break:break-all;display:inline-block;max-width:260px}
        .pill{display:inline-block;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600}
        .pill-on{background:#dcfce7;color:#166534}
        .pill-off{background:#f3f4f6;color:#6b7280}
        form.inline{display:inline}
        .btn{padding:6px 12px;border-radius:5px;border:1px solid var(--line);background:#fff;font-size:13px;cursor:pointer;color:#111827;text-decoration:none;display:inline-block}
        .btn:hover{background:#f8fafc}
        .btn-primary{background:var(--c);color:#fff;border-color:var(--c)}
        .btn-primary:hover{background:#1e40af}
        .btn-danger{background:#dc2626;color:#fff;border-color:#dc2626}
        .btn-danger:hover{background:#991b1b}
        label{font-weight:600;font-size:13px;display:block;margin-bottom:4px}
        input[type=text],textarea{width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:6px;font-size:14px;font-family:inherit}
        textarea{min-height:70px;resize:vertical}
        .hint{color:var(--muted);font-size:12.5px;margin-top:4px}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        @media(max-width:640px){.grid{grid-template-columns:1fr}}
        pre.snippet{background:#0f172a;color:#e2e8f0;padding:14px;border-radius:6px;overflow:auto;font-size:12.5px;font-family:ui-monospace,SFMono-Regular,monospace}
        .empty{color:var(--muted);padding:24px 0;text-align:center}

        /* Tutorial styles */
        .steps{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin:14px 0 6px}
        .step{background:#f8fafc;border:1px solid var(--line);border-radius:8px;padding:14px;position:relative}
        .step .num{position:absolute;top:-12px;left:14px;width:26px;height:26px;border-radius:9999px;background:var(--c);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700}
        .step h3{margin:6px 0 6px;font-size:14px}
        .step p{margin:0;font-size:13px;color:#374151;line-height:1.5}
        .step code{font-size:12px}
        .preview{display:flex;justify-content:center;background:#f8fafc;border:1px dashed var(--line);border-radius:8px;padding:24px;margin-top:14px}
        .preview-page{position:relative;background:#fff;border:1px solid var(--line);border-radius:6px;width:280px;height:170px;box-shadow:0 4px 10px rgba(0,0,0,.05)}
        .preview-bar{height:24px;background:#f1f5f9;border-bottom:1px solid var(--line);display:flex;align-items:center;padding:0 8px;gap:4px;border-radius:6px 6px 0 0}
        .preview-bar i{width:10px;height:10px;border-radius:9999px;background:#cbd5e1;display:block}
        .preview-content{padding:14px;font-size:11px;color:#6b7280}
        .preview-content div{height:8px;background:#e5e7eb;border-radius:4px;margin-bottom:6px}
        .preview-content div:nth-child(2){width:70%}
        .preview-fab{position:absolute;bottom:10px;right:10px;width:34px;height:34px;border-radius:9999px;background:var(--c);box-shadow:0 4px 10px rgba(37,99,235,.4);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;animation:pulse 2s ease-in-out infinite}
        @keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.08)}}
        .faq details{border:1px solid var(--line);border-radius:6px;margin-bottom:8px;padding:0;background:#fff}
        .faq summary{padding:10px 14px;font-size:14px;font-weight:500;cursor:pointer;list-style:none;display:flex;justify-content:space-between;align-items:center}
        .faq summary::-webkit-details-marker{display:none}
        .faq summary::after{content:'+';color:var(--muted);font-size:18px}
        .faq details[open] summary::after{content:'\2212'}
        .faq details[open] summary{border-bottom:1px solid var(--line)}
        .faq .ans{padding:12px 14px;font-size:13.5px;color:#374151;line-height:1.6}
        .faq .ans code{font-size:12px}
        .tip{background:#fef3c7;border:1px solid #fde68a;border-radius:6px;padding:10px 14px;font-size:13px;color:#78350f;margin-top:14px}
        .tip strong{display:block;margin-bottom:2px;color:#92400e}

        /* Tab strip */
        .tab-strip{background:#fff;border:1px solid var(--line);border-radius:8px;padding:6px;margin-bottom:18px;display:flex;gap:4px;flex-wrap:wrap;position:sticky;top:0;z-index:10}
        .tab-strip button{background:transparent;border:none;padding:8px 14px;border-radius:6px;font-size:13.5px;font-weight:500;color:var(--muted);cursor:pointer;transition:all .15s;font-family:inherit}
        .tab-strip button:hover{background:#f1f5f9;color:var(--ink)}
        .tab-strip button.active{background:var(--c);color:#fff}
        .tab-pane{display:none}
        .tab-pane.active{display:block}
    </style>
</head>
<body>
    <header>
        <h1>Bug Widget — {{ $project->title }}</h1>
        <div style="display:flex;gap:14px;align-items:center">
            <a href="{{ url('/tutorials#bug-widget') }}">📚 Full tutorials</a>
            <a href="{{ url('/projects/' . $project->id) }}">&larr; Back to project</a>
        </div>
    </header>
    <div class="wrap">
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        <div class="tab-strip" role="tablist">
            <button type="button" data-tab="keys" class="active">🔑 Keys &amp; install</button>
            <button type="button" data-tab="routing">🎯 Routing</button>
            <button type="button" data-tab="webhooks">🔔 Webhooks</button>
            <button type="button" data-tab="boards">🌐 Public boards</button>
            <button type="button" data-tab="sla">⏰ SLA &amp; retention</button>
            <button type="button" data-tab="help">❓ Help</button>
        </div>

        <div class="tab-pane active" data-pane="keys">
        <div class="card">
            <h2>Setup tutorial — 4 steps to live feedback</h2>

            <div class="steps">
                <div class="step">
                    <div class="num">1</div>
                    <h3>Generate a key</h3>
                    <p>Use the form below. Name it after the site you'll install on ("Production", "Staging client X").</p>
                </div>
                <div class="step">
                    <div class="num">2</div>
                    <h3>Add allowed origins</h3>
                    <p>List the exact URLs allowed to use this key — one per line. Example: <code>https://example.com</code>. This stops anyone else from using your key.</p>
                </div>
                <div class="step">
                    <div class="num">3</div>
                    <h3>Copy &amp; paste the snippet</h3>
                    <p>Drop one line into the &lt;head&gt; or before &lt;/body&gt; of the target site. No build step needed.</p>
                </div>
                <div class="step">
                    <div class="num">4</div>
                    <h3>Visitors send feedback</h3>
                    <p>A floating <strong>Feedback</strong> button appears. Pins + screenshots flow into this project's bug list.</p>
                </div>
            </div>

            <div class="preview" title="What visitors see on the target site">
                <div class="preview-page">
                    <div class="preview-bar"><i></i><i></i><i></i></div>
                    <div class="preview-content"><div></div><div></div><div></div></div>
                    <div class="preview-fab">💬</div>
                </div>
            </div>
            <p class="hint" style="text-align:center;margin-top:6px">Preview: floating button visitors see in the bottom-right corner.</p>

            <p style="margin:18px 0 6px;font-size:14px;color:#374151"><strong>Install snippet</strong> (replace <em>YOUR_KEY</em> with one from the list below):</p>
            <pre class="snippet">&lt;script src="{{ $scriptUrl }}" data-key="<em>YOUR_KEY</em>" async&gt;&lt;/script&gt;</pre>
            <p class="hint" style="margin-top:10px">Feedback endpoint: <code>{{ $endpoint }}</code></p>

            <div class="tip">
                <strong>Tip — test locally first</strong>
                Add <code>http://localhost</code> (and any localhost port like <code>http://localhost:3000</code>) to allowed origins while developing. Switch to your production origin before going live.
            </div>
        </div>

        <div class="card">
            <h2>Create new widget key</h2>
            <form method="POST" action="{{ url('/projects/' . $project->id . '/widget-keys') }}">
                @csrf
                <div class="grid">
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" required maxlength="100" placeholder="Production site">
                        <p class="hint">A label to recognise the key later.</p>
                    </div>
                    <div>
                        <label>Allowed origins</label>
                        <textarea name="allowed_origins" placeholder="https://example.com&#10;https://staging.example.com&#10;or * for any"></textarea>
                        <p class="hint">One per line. Leave blank or <code>*</code> to allow any origin (not recommended).</p>
                    </div>
                </div>
                <details style="margin-top:12px">
                    <summary style="cursor:pointer;font-weight:500;color:var(--c);font-size:13px">🎨 Branding (optional white-label)</summary>
                    <div class="grid" style="margin-top:10px">
                        <div>
                            <label>Brand colour</label>
                            <input type="text" name="brand_color" placeholder="#16a34a" maxlength="16">
                            <p class="hint">Hex code like <code>#16a34a</code>. Overrides the default blue.</p>
                        </div>
                        <div>
                            <label>Logo URL (32x32 recommended)</label>
                            <input type="text" name="brand_logo_url" placeholder="https://example.com/logo.png" maxlength="512">
                            <p class="hint">Replaces the chat icon inside the floating button.</p>
                        </div>
                    </div>
                    <div class="grid">
                        <div>
                            <label>Button label / aria-label</label>
                            <input type="text" name="button_label" placeholder="Send feedback" maxlength="60">
                        </div>
                        <div>
                            <label>Welcome line (first-visit tooltip)</label>
                            <input type="text" name="welcome_text" placeholder="Found a glitch? Tell us." maxlength="280">
                        </div>
                    </div>
                </details>
                <div style="margin-top:14px">
                    <button type="submit" class="btn btn-primary">Generate key</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Existing keys ({{ $keys->count() }})</h2>
            @if ($keys->isEmpty())
                <div class="empty">No keys yet — create one above.</div>
            @else
                <table>
                    <thead><tr>
                        <th>Name</th><th>Key</th><th>Origins</th><th>Status</th><th>Last used</th><th></th>
                    </tr></thead>
                    <tbody>
                    @foreach ($keys as $k)
                        <tr>
                            <td>{{ $k->name }}</td>
                            <td><span class="key-copy" onclick="copyText(this)">{{ $k->public_key }}</span></td>
                            <td>
                                @php $origins = $k->allowed_origins ?? []; @endphp
                                @if (empty($origins))
                                    <em style="color:#6b7280">any</em>
                                @else
                                    @foreach ($origins as $o)<code style="display:inline-block;margin:2px 0">{{ $o }}</code> @endforeach
                                @endif
                            </td>
                            <td>
                                @if ($k->is_enabled)
                                    <span class="pill pill-on">enabled</span>
                                @else
                                    <span class="pill pill-off">disabled</span>
                                @endif
                            </td>
                            <td>{{ $k->last_used_at?->diffForHumans() ?? '—' }}</td>
                            <td style="white-space:nowrap">
                                <form class="inline" method="POST" action="{{ url('/projects/' . $project->id . '/widget-keys/' . $k->id . '/toggle') }}">
                                    @csrf
                                    <button class="btn" type="submit">{{ $k->is_enabled ? 'Disable' : 'Enable' }}</button>
                                </form>
                                <form class="inline" method="POST" action="{{ url('/projects/' . $project->id . '/widget-keys/' . $k->id) }}" onsubmit="return confirm('Delete this key? Sites using it will stop sending feedback.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        </div>{{-- /keys pane --}}

        <div class="tab-pane" data-pane="routing">
        <div class="card">
            <h2>Auto-assign rules</h2>
            <p style="margin:0 0 12px;font-size:14px;color:#374151">
                When a widget submission comes in, the first matching URL pattern (top to bottom by sort order) auto-assigns the bug
                and optionally overrides the priority. Patterns use <code>*</code> as wildcard.
                Example: <code>https://example.com/checkout/*</code>.
            </p>

            <form method="POST" action="{{ url('/projects/' . $project->id . '/widget-routes') }}">
                @csrf
                <div class="grid" style="grid-template-columns:2fr 1fr 1fr 80px;gap:10px;align-items:end">
                    <div>
                        <label>URL pattern</label>
                        <input type="text" name="url_pattern" required maxlength="512" placeholder="https://example.com/checkout/*">
                    </div>
                    <div>
                        <label>Assignee</label>
                        <select name="assignee_id" style="width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:6px;font-size:14px">
                            <option value="">— none —</option>
                            @foreach ($members as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Priority override</label>
                        <select name="priority_override" style="width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:6px;font-size:14px">
                            <option value="">— keep reporter's —</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div>
                        <label>Order</label>
                        <input type="number" name="sort_order" value="100" min="0" max="1000" style="width:100%">
                    </div>
                </div>
                <div style="margin-top:12px">
                    <button type="submit" class="btn btn-primary">Add rule</button>
                </div>
            </form>

            <table style="margin-top:18px">
                <thead><tr>
                    <th>Pattern</th><th>Assignee</th><th>Priority</th><th>Order</th><th>Status</th><th></th>
                </tr></thead>
                <tbody>
                    @forelse ($routes as $r)
                        <tr>
                            <td><code>{{ $r->url_pattern }}</code></td>
                            <td>{{ $r->assignee?->name ?? '—' }}</td>
                            <td>{{ $r->priority_override ?? '—' }}</td>
                            <td>{{ $r->sort_order }}</td>
                            <td>
                                @if ($r->is_enabled)
                                    <span class="pill pill-on">enabled</span>
                                @else
                                    <span class="pill pill-off">disabled</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap">
                                <form class="inline" method="POST" action="{{ url('/projects/' . $project->id . '/widget-routes/' . $r->id . '/toggle') }}">
                                    @csrf
                                    <button class="btn" type="submit">{{ $r->is_enabled ? 'Disable' : 'Enable' }}</button>
                                </form>
                                <form class="inline" method="POST" action="{{ url('/projects/' . $project->id . '/widget-routes/' . $r->id) }}" onsubmit="return confirm('Remove this routing rule?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">No routing rules — incoming bugs land unassigned with reporter's priority.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        </div>{{-- /routing pane --}}

        <div class="tab-pane" data-pane="webhooks">
        <div class="card">
            <h2>Outgoing webhooks (Slack / Teams / Discord)</h2>
            <p style="margin:0 0 12px;font-size:14px;color:#374151">
                Get notified in your team chat when a new bug arrives, gets assigned, or changes status.
                The format is auto-detected from the webhook URL — works with Slack incoming webhooks, Microsoft Teams connectors, and Discord webhooks.
                Anything else falls back to a generic JSON payload.
            </p>

            <form method="POST" action="{{ url('/projects/' . $project->id . '/webhooks') }}">
                @csrf
                <div class="grid" style="grid-template-columns:1fr 2fr;gap:10px">
                    <div>
                        <label>Label</label>
                        <input type="text" name="name" required maxlength="100" placeholder="#bugs Slack channel">
                    </div>
                    <div>
                        <label>Webhook URL</label>
                        <input type="text" name="target_url" required maxlength="512" placeholder="https://hooks.slack.com/services/...">
                    </div>
                </div>
                <div style="margin-top:8px">
                    <label style="display:inline;margin-right:14px;font-weight:500">Send on:</label>
                    <label style="display:inline;margin-right:14px;font-weight:400"><input type="checkbox" name="events[]" value="bug.created" checked> New bug</label>
                    <label style="display:inline;margin-right:14px;font-weight:400"><input type="checkbox" name="events[]" value="bug.assigned" checked> Assigned</label>
                    <label style="display:inline;font-weight:400"><input type="checkbox" name="events[]" value="bug.status_changed" checked> Status changed</label>
                </div>
                <div style="margin-top:14px">
                    <button type="submit" class="btn btn-primary">Add webhook</button>
                </div>
            </form>

            <table style="margin-top:18px">
                <thead><tr>
                    <th>Label</th><th>Target</th><th>Events</th><th>Status</th><th>Last sent</th><th></th>
                </tr></thead>
                <tbody>
                    @forelse ($webhooks ?? [] as $h)
                        <tr>
                            <td>{{ $h->name }}<br><span style="font-size:11px;color:#6b7280;text-transform:uppercase">{{ $h->detectPlatform() }}</span></td>
                            <td><code style="word-break:break-all;font-size:11px">{{ \Illuminate\Support\Str::limit($h->target_url, 60) }}</code></td>
                            <td>
                                @foreach ($h->events ?? [] as $e)
                                    <code style="font-size:10.5px;display:inline-block;margin:2px 0">{{ str_replace('bug.', '', $e) }}</code>
                                @endforeach
                            </td>
                            <td>
                                @if ($h->is_enabled)
                                    <span class="pill pill-on">enabled</span>
                                @else
                                    <span class="pill pill-off">disabled</span>
                                @endif
                                @if ($h->fail_count > 0)
                                    <br><small style="color:#dc2626">{{ $h->fail_count }} fails</small>
                                @endif
                            </td>
                            <td>{{ $h->last_sent_at?->diffForHumans() ?? '—' }}</td>
                            <td style="white-space:nowrap">
                                <form class="inline" method="POST" action="{{ url('/projects/' . $project->id . '/webhooks/' . $h->id . '/test') }}">
                                    @csrf
                                    <button class="btn" type="submit">Send test</button>
                                </form>
                                <form class="inline" method="POST" action="{{ url('/projects/' . $project->id . '/webhooks/' . $h->id . '/toggle') }}">
                                    @csrf
                                    <button class="btn" type="submit">{{ $h->is_enabled ? 'Disable' : 'Enable' }}</button>
                                </form>
                                <form class="inline" method="POST" action="{{ url('/projects/' . $project->id . '/webhooks/' . $h->id) }}" onsubmit="return confirm('Remove this webhook?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">No webhooks yet. Add one above to push notifications to Slack / Teams / Discord.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        </div>{{-- /webhooks pane --}}

        <div class="tab-pane" data-pane="boards">
        <div class="card">
            <h2>Public read-only boards</h2>
            <p style="margin:0 0 12px;font-size:14px;color:#374151">
                Generate a magic-link URL clients can open without logging in to see all feedback on this project — kanban-style list with search and status filter.
                Toggle screenshots on/off and limit to widget-source bugs only (hides internal team-only bugs).
            </p>

            <form method="POST" action="{{ url('/projects/' . $project->id . '/public-boards') }}">
                @csrf
                <div class="grid" style="grid-template-columns:1fr auto auto;gap:10px;align-items:end">
                    <div>
                        <label>Board name</label>
                        <input type="text" name="name" required maxlength="100" placeholder="Client X — production">
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:6px;font-weight:400">
                            <input type="checkbox" name="show_widget_only" value="1"> Widget bugs only
                        </label>
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:6px;font-weight:400">
                            <input type="checkbox" name="show_screenshots" value="1" checked> Show screenshots
                        </label>
                    </div>
                </div>
                <div style="margin-top:12px">
                    <button type="submit" class="btn btn-primary">Create public board</button>
                </div>
            </form>

            <table style="margin-top:18px">
                <thead><tr><th>Name</th><th>Share URL</th><th>Mode</th><th>Status</th><th>Last viewed</th><th></th></tr></thead>
                <tbody>
                @forelse ($publicBoards ?? [] as $b)
                    <tr>
                        <td>{{ $b->name }}</td>
                        <td><span class="key-copy" onclick="copyText(this)">{{ url('/board/' . $b->share_token) }}</span></td>
                        <td style="font-size:11px">
                            {{ $b->show_widget_only ? 'widget only' : 'all bugs' }}<br>
                            {{ $b->show_screenshots ? 'with screenshots' : 'no screenshots' }}
                        </td>
                        <td>
                            @if ($b->is_enabled)
                                <span class="pill pill-on">enabled</span>
                            @else
                                <span class="pill pill-off">disabled</span>
                            @endif
                        </td>
                        <td>{{ $b->last_viewed_at?->diffForHumans() ?? '—' }}</td>
                        <td style="white-space:nowrap">
                            <a class="btn" target="_blank" rel="noopener" href="{{ url('/board/' . $b->share_token) }}">Open</a>
                            <form class="inline" method="POST" action="{{ url('/projects/' . $project->id . '/public-boards/' . $b->id . '/toggle') }}">
                                @csrf
                                <button class="btn" type="submit">{{ $b->is_enabled ? 'Disable' : 'Enable' }}</button>
                            </form>
                            <form class="inline" method="POST" action="{{ url('/projects/' . $project->id . '/public-boards/' . $b->id) }}" onsubmit="return confirm('Delete this board?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No public boards yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        </div>{{-- /boards pane --}}

        <div class="tab-pane" data-pane="sla">
        <div class="card">
            <h2>SLA targets</h2>
            <p style="margin:0 0 12px;font-size:14px;color:#374151">
                Hours allowed before a bug should be resolved, per priority. New bugs get a <code>due_at</code> stamp based on these targets — overdue bugs show a red badge on detail pages.
            </p>
            <form method="POST" action="{{ url('/projects/' . $project->id . '/sla') }}">
                @csrf
                <table>
                    <thead><tr><th>Priority</th><th>Respond within (hrs)</th><th>Resolve within (hrs)</th></tr></thead>
                    <tbody>
                        @php $idx = 0; @endphp
                        @foreach (['critical','high','medium','low'] as $p)
                            <tr>
                                <td>
                                    <strong style="text-transform:capitalize">{{ $p }}</strong>
                                    <input type="hidden" name="policies[{{ $idx }}][priority]" value="{{ $p }}">
                                </td>
                                <td><input type="number" min="1" max="8760" name="policies[{{ $idx }}][respond_hours]" value="{{ $slaPolicies[$p]['respond_hours'] }}" style="width:90px"></td>
                                <td><input type="number" min="1" max="8760" name="policies[{{ $idx }}][resolve_hours]" value="{{ $slaPolicies[$p]['resolve_hours'] }}" style="width:90px"></td>
                            </tr>
                            @php $idx++; @endphp
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top:12px"><button class="btn btn-primary" type="submit">Save SLA</button></div>
            </form>
        </div>

        <div class="card">
            <h2>Data retention (GDPR)</h2>
            <p style="margin:0 0 12px;font-size:14px;color:#374151">
                Auto-delete old bugs (and their screenshots / videos) past N days. Runs nightly via the scheduler. Leave blank to keep all bugs forever.
            </p>
            <form method="POST" action="{{ url('/projects/' . $project->id . '/retention') }}">
                @csrf
                <div class="grid" style="grid-template-columns:200px 1fr;gap:14px;align-items:end">
                    <div>
                        <label>Retention (days)</label>
                        <input type="number" name="bug_retention_days" min="0" max="3650" value="{{ $project->bug_retention_days }}" placeholder="forever">
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:6px;font-weight:400">
                            <input type="checkbox" name="retention_widget_only" value="1" {{ $project->retention_widget_only ? 'checked' : '' }}>
                            Apply only to widget-source bugs (leave internal team bugs alone)
                        </label>
                    </div>
                </div>
                <div style="margin-top:12px"><button type="submit" class="btn btn-primary">Save retention</button></div>
            </form>
            <p class="hint" style="margin-top:8px">Test now: <code>php artisan bugs:retention-cleanup --dry-run</code></p>

            <details style="margin-top:12px;background:#f8fafc;border:1px solid var(--line);border-radius:6px;padding:10px 14px">
                <summary style="cursor:pointer;font-weight:500;font-size:13.5px">⏰ Enable nightly cleanup on Windows (one-time)</summary>
                <p style="font-size:13px;margin:10px 0 6px;color:#374151">Open <strong>PowerShell as Administrator</strong> and run:</p>
                <pre class="snippet">schtasks /Create /SC MINUTE /MO 1 /TN "Gesture\schedule-run" /TR "c:\xampp82\htdocs\task\gesture-schedule.bat" /RL HIGHEST /F</pre>
                <p style="font-size:12.5px;color:#6b7280;margin:8px 0 0">This fires the Laravel scheduler every minute (lightweight). The retention command itself only runs at 03:00 daily.
                Log file: <code>storage/logs/schedule.log</code>. To remove: <code>schtasks /Delete /TN "Gesture\schedule-run" /F</code></p>
            </details>
        </div>

        </div>{{-- /sla pane (data retention is also in sla pane) --}}

        <div class="tab-pane" data-pane="help">
        <div class="card faq">
            <h2>Common questions</h2>

            <details>
                <summary>Where does the feedback go?</summary>
                <div class="ans">
                    Straight into this project's <a href="{{ url('/bugs') }}">Bugs</a> list with <code>source = widget</code>.
                    Each entry stores: description, screenshot, the URL where it happened, the CSS selector of the clicked element, pin coordinates,
                    viewport size, browser, OS, and the reporter's name/email if provided.
                    Click any widget-sourced bug → "View widget data" to see the pinned screenshot.
                </div>
            </details>

            <details>
                <summary>Do my visitors need an account?</summary>
                <div class="ans">
                    No. The widget is fully anonymous — visitors can submit feedback as a guest. They can optionally add their name/email,
                    which becomes the reporter contact on the bug.
                </div>
            </details>

            <details>
                <summary>What if someone copies my key?</summary>
                <div class="ans">
                    They can't use it — the <strong>Origin allowlist</strong> blocks requests coming from any site you didn't authorise.
                    If a key is compromised, click <strong>Delete</strong> here and the offending site stops working instantly.
                    Then create a new key and update the snippet on your real site.
                </div>
            </details>

            <details>
                <summary>Why does my screenshot look incomplete or empty?</summary>
                <div class="ans">
                    Screenshots use <a href="https://html2canvas.hertzen.com/" target="_blank" rel="noopener">html2canvas</a>, which renders the visible viewport.
                    Two common limits: (1) cross-origin images / iframes can't be captured (they render as blank) — host assets on the same origin or use CORS;
                    (2) very tall pages capture only the viewport, not the full page.
                </div>
            </details>

            <details>
                <summary>How do I install on a SPA (React / Vue / Next.js)?</summary>
                <div class="ans">
                    Same way — drop the &lt;script&gt; in your <code>index.html</code> (CRA / Vite) or root layout (Next.js, Nuxt).
                    The widget is framework-agnostic and only initialises once per page load (guarded by <code>window.__GestureBugWidgetLoaded</code>).
                </div>
            </details>

            <details>
                <summary>Can I customise the colour or position?</summary>
                <div class="ans">
                    Yes — extra attributes on the script tag:
                    <pre class="snippet" style="margin-top:8px">&lt;script src="{{ $scriptUrl }}"
        data-key="YOUR_KEY"
        data-color="#16a34a"
        data-position="bottom-left"
        async&gt;&lt;/script&gt;</pre>
                    <code>data-position</code> accepts <code>bottom-right</code> (default) or <code>bottom-left</code>.
                </div>
            </details>

            <details>
                <summary>What about spam / abuse?</summary>
                <div class="ans">
                    There's a built-in rate limit of 20 submissions/minute per IP per key. Disable a noisy key with the
                    <strong>Disable</strong> button (keeps it in the list so you can re-enable later) or <strong>Delete</strong> to remove permanently.
                </div>
            </details>

            <details>
                <summary>Can I assign incoming bugs automatically?</summary>
                <div class="ans">
                    Not yet — currently widget submissions land in the project's <em>default bug status</em> (or the first ordered status).
                    Assign them from the Bugs board like any other bug. A future enhancement could auto-assign by URL pattern.
                </div>
            </details>

            <details>
                <summary>How big is the widget — will it slow my site down?</summary>
                <div class="ans">
                    The widget script is <strong>~15&nbsp;KB</strong> (loaded <code>async</code>, so it never blocks rendering).
                    The screenshot library (<code>html2canvas</code>, ~300&nbsp;KB) only loads <em>after</em> a visitor clicks the feedback button — never on first paint.
                </div>
            </details>
        </div>
        </div>{{-- /help pane --}}

    </div>

    <script>
        // Tab strip
        (function () {
            var buttons = document.querySelectorAll('.tab-strip button');
            var panes = document.querySelectorAll('.tab-pane');
            function activate(name) {
                buttons.forEach(function (b) { b.classList.toggle('active', b.dataset.tab === name); });
                panes.forEach(function (p) { p.classList.toggle('active', p.dataset.pane === name); });
                try { history.replaceState(null, '', '#tab-' + name); } catch (e) {}
            }
            buttons.forEach(function (b) {
                b.addEventListener('click', function () { activate(b.dataset.tab); });
            });
            var hash = (location.hash || '').replace(/^#tab-/, '');
            if (hash) activate(hash);
        })();

        function copyText(node) {
            var t = node.textContent.trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(t).then(function () {
                    var orig = node.textContent;
                    node.textContent = 'Copied!';
                    setTimeout(function () { node.textContent = orig; }, 1000);
                });
            }
        }
    </script>
</body>
</html>
