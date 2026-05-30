<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $project->title }} — {{ $start->format('F Y') }} Report</title>
    <style>
        :root{
            --c:#10b981;       /* Gesture green */
            --c2:#111827;
            --bg:#f8fafc;
            --line:#e5e7eb;
            --muted:#6b7280;
            --ink:#111827;
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:var(--bg);color:var(--ink);line-height:1.55}
        header.app{background:#fff;border-bottom:1px solid var(--line);padding:14px 24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
        header.app h1{margin:0;font-size:18px}
        header.app a{color:var(--c);text-decoration:none;font-size:14px}
        .nav{display:flex;gap:8px;font-size:13px}
        .nav a{padding:6px 12px;border:1px solid var(--line);border-radius:6px;color:var(--ink)}
        .nav a.current{background:var(--c2);color:#fff;border-color:var(--c2)}
        .wrap{max-width:1100px;margin:24px auto;padding:0 16px}

        /* Cover page */
        .cover{background:linear-gradient(135deg,var(--c2),#1f2937);color:#fff;padding:40px 36px;border-radius:12px;margin-bottom:24px}
        .cover .badge{display:inline-block;font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:var(--c);font-weight:600;margin-bottom:8px}
        .cover h2{font-size:32px;margin:4px 0 6px;font-weight:700}
        .cover p.sub{margin:0;font-size:14px;opacity:.85}
        .cover .meta{margin-top:18px;font-size:13px;opacity:.7}

        /* Table of contents */
        .toc{background:#fff;border:1px solid var(--line);border-radius:10px;padding:18px 20px;margin-bottom:24px}
        .toc h3{margin:0 0 10px;font-size:13px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
        .toc ol{margin:0;padding-left:0;list-style:none;counter-reset:item}
        .toc ol li{counter-increment:item;padding:6px 0;border-bottom:1px dotted var(--line);font-size:14px}
        .toc ol li:last-child{border-bottom:none}
        .toc ol li::before{content:counter(item, decimal-leading-zero) "  ";color:var(--c);font-weight:700;margin-right:8px}
        .toc a{color:var(--ink);text-decoration:none}
        .toc a:hover{color:var(--c)}

        /* Section */
        section.report{background:#fff;border:1px solid var(--line);border-radius:10px;padding:24px 28px;margin-bottom:22px;scroll-margin-top:14px}
        section.report h2{margin:0 0 6px;font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:var(--c);font-weight:700}
        section.report h3{margin:4px 0 14px;font-size:22px;font-weight:700;color:var(--c2)}
        section.report p.intro{font-size:14px;color:#374151;line-height:1.6}

        /* KPI grid */
        .kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin:16px 0}
        .kpi{background:#f8fafc;border:1px solid var(--line);border-radius:8px;padding:14px 16px;position:relative}
        .kpi .label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
        .kpi .value{font-size:30px;font-weight:700;color:var(--c2);margin-top:4px;line-height:1.1}
        .kpi .hint{font-size:11.5px;color:var(--muted);margin-top:4px}
        .kpi .edit{position:absolute;top:8px;right:8px;font-size:11px;color:var(--muted);text-decoration:none;padding:2px 6px;border-radius:4px;opacity:0;transition:opacity .15s}
        .kpi:hover .edit{opacity:1}
        .kpi .edit:hover{background:var(--c);color:#fff}

        /* Tasks list */
        ul.tasks{list-style:none;padding:0;margin:8px 0 0}
        ul.tasks li{padding:8px 0;border-bottom:1px solid var(--line);font-size:14px;display:flex;justify-content:space-between;gap:10px;align-items:flex-start}
        ul.tasks li:last-child{border-bottom:none}
        ul.tasks .check{color:var(--c);font-weight:700}
        ul.tasks .when{font-size:11.5px;color:var(--muted);white-space:nowrap}

        /* Keyword tables */
        .kw-section{margin-top:14px}
        .kw-section h4{margin:14px 0 8px;font-size:13px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em}
        table.kw{width:100%;border-collapse:collapse;font-size:13.5px}
        table.kw th{text-align:left;padding:8px 10px;border-bottom:2px solid var(--line);color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.06em}
        table.kw td{padding:8px 10px;border-bottom:1px solid var(--line)}
        table.kw .pos{font-weight:700;color:var(--c2)}
        table.kw .delta-up{color:#16a34a;font-weight:600}
        table.kw .delta-down{color:#dc2626;font-weight:600}
        table.kw .delta-flat{color:var(--muted)}
        table.kw .empty{padding:24px;text-align:center;color:var(--muted);font-style:italic}

        /* Edit form */
        details.edit{background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:14px 18px;margin-bottom:22px}
        details.edit summary{cursor:pointer;font-weight:600;font-size:14px;color:#92400e}
        details.edit input,details.edit select{padding:6px 10px;border:1px solid var(--line);border-radius:5px;font-size:13px;width:100%;font-family:inherit}
        details.edit .grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:12px}
        details.edit .grid-4{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:10px;margin-top:8px}
        details.edit label{display:block;font-size:11.5px;color:#92400e;margin-bottom:3px;font-weight:600}
        details.edit button{margin-top:14px;background:#d97706;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer}

        .flash{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:14px}

        /* Footer */
        footer.thanks{background:linear-gradient(135deg,var(--c),#059669);color:#fff;padding:30px 36px;border-radius:12px;margin:24px 0;text-align:center}
        footer.thanks h3{margin:0 0 6px;font-size:22px}
        footer.thanks p{margin:0;font-size:13px;opacity:.92}

        @media print{
            header.app, details.edit, .nav{display:none}
            body{background:#fff}
            section.report{break-inside:avoid;border:none;box-shadow:none;padding:18px 0}
            .wrap{max-width:none;padding:0 24px}
            .cover, footer.thanks{break-inside:avoid}
        }
    </style>
</head>
<body>

<header class="app">
    <div>
        <h1>{{ $project->title }} — Monthly Report</h1>
        <div style="font-size:12px;color:var(--muted)">{{ $start->format('F Y') }}</div>
    </div>
    <div class="nav">
        <a href="{{ url('/projects/' . $project->id . '/reports?month=' . $prevMonth) }}">← {{ Carbon\Carbon::parse($prevMonth)->format('M Y') }}</a>
        <a href="#" class="current">{{ $start->format('M Y') }}</a>
        <a href="{{ url('/projects/' . $project->id . '/reports?month=' . $nextMonth) }}">{{ Carbon\Carbon::parse($nextMonth)->format('M Y') }} →</a>
        <a href="{{ url('/projects/' . $project->id . '/reports.pdf?month=' . $start->format('Y-m')) }}" title="Download PDF">📄 Download PDF</a>
        <a href="javascript:window.print()" title="Print">🖨 Print</a>
        <a href="{{ url('/projects/' . $project->id) }}">Back to project</a>
    </div>
</header>

<div class="wrap">

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    <p style="font-size:13px;color:var(--muted);margin:0 0 12px">
        Configure GA4 / GSC / GBP integrations on the
        <a href="{{ url('/projects/' . $project->id) }}" style="color:var(--c)">project page</a>.
    </p>

    {{-- Manual KPI editor (open by default — primary way to update the report) --}}
    <details class="edit" id="manual-editor" open style="background:#fff;border-color:#10b981;border-width:2px">
        <summary style="font-size:15px;color:#065f46">✏️ <strong>Manually enter / update this month's results</strong> — overrides any sync</summary>
        <p style="font-size:12.5px;color:#374151;margin:10px 0 4px">Type the numbers below and click <strong>Save report data</strong> at the bottom. Empty fields stay unchanged.</p>
        <form method="POST" action="{{ url('/projects/' . $project->id . '/reports') }}" style="margin-top:14px">
            @csrf
            <input type="hidden" name="period" value="{{ $start->format('Y-m') }}">

            <h4 style="margin:0 0 6px;color:#92400e">Headline KPIs</h4>
            <div class="grid-3">
                @foreach (App\Models\ProjectMetric::KEYS as $k => $label)
                    <div>
                        <label>{{ $label }}</label>
                        <input type="number" step="0.01" min="0" name="metrics[{{ $k }}]" value="{{ $metrics[$k] ?? '' }}">
                    </div>
                @endforeach
            </div>

            <h4 style="margin:18px 0 6px;color:#92400e">Keyword rankings</h4>
            <div class="grid-4" style="font-weight:600;font-size:11px;color:#92400e;text-transform:uppercase">
                <div>Keyword</div><div>Current pos.</div><div>Previous pos.</div><div>Bucket</div>
            </div>
            @php
                $allKw = collect($rankings ?? [])->flatten(1);
                $rows = max(8, $allKw->count() + 2);
            @endphp
            @for ($i = 0; $i < $rows; $i++)
                @php $kw = $allKw[$i] ?? null; @endphp
                <div class="grid-4">
                    <input type="text" name="keywords[{{ $i }}][keyword]" placeholder="e.g. underpinning sydney" value="{{ $kw->keyword ?? '' }}">
                    <input type="number" min="1" max="100" name="keywords[{{ $i }}][position]" placeholder="1-100" value="{{ $kw->position ?? '' }}">
                    <input type="number" min="1" max="100" name="keywords[{{ $i }}][previous]" placeholder="last month" value="{{ $kw->previous_position ?? '' }}">
                    <select name="keywords[{{ $i }}][bucket]">
                        @foreach (App\Models\ProjectKeywordRanking::BUCKETS as $val => $lbl)
                            <option value="{{ $val }}" {{ ($kw && $kw->bucket === $val) ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            @endfor

            <button type="submit">💾 Save report data</button>
        </form>
    </details>

    {{-- COVER --}}
    <div class="cover">
        <div class="badge">Prepared with your business' success in mind</div>
        <h2>{{ $project->title }}</h2>
        <p class="sub">Digital monthly report for <strong>{{ strtoupper($start->format('F Y')) }}</strong></p>
        <div class="meta">
            @if ($project->live_url)
                {{ parse_url($project->live_url, PHP_URL_HOST) }}
            @endif
            · Service: {{ $project->description ?? 'Web Development' }}
        </div>
    </div>

    {{-- TOC --}}
    <div class="toc">
        <h3>Overview</h3>
        <ol>
            <li><a href="#section-1">Website Performance</a></li>
            <li><a href="#section-2">Leads</a></li>
            <li><a href="#section-3">SEO Rankings &amp; Progress</a></li>
            <li><a href="#section-4">Google My Business Performance</a></li>
            <li><a href="#section-5">AI Search Visibility</a></li>
            <li><a href="#section-6">Work We've Done</a></li>
            <li><a href="#section-7">Work Ahead for Next Month</a></li>
        </ol>
    </div>

    {{-- SECTION 1: WEBSITE PERFORMANCE --}}
    <section class="report" id="section-1">
        <h2>1. Website performance</h2>
        <h3>Traffic, users &amp; engagement</h3>
        <p class="intro">A fast, well-performing site is essential for online success. It delivers a frictionless user experience, satisfies search engine speed signals, and increases the chance that visitors convert into customers.</p>

        <div class="kpi-grid">
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">Website sessions</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['sessions'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">{{ $start->format('F Y') }} · GA4</div>
            </div>
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">Total users</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['users'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">{{ $start->format('F Y') }} · GA4</div>
            </div>
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">Page views</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['page_views'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">{{ $start->format('F Y') }} · GA4</div>
            </div>
        </div>

        <p class="intro" style="margin-top:8px">
            <strong>{{ $project->title }}</strong> accumulated
            <strong>{{ rtrim(rtrim(number_format($metrics['sessions'] ?? 0, 2), '0'), '.') ?: '—' }}</strong> website sessions and
            <strong>{{ rtrim(rtrim(number_format($metrics['users'] ?? 0, 2), '0'), '.') ?: '—' }}</strong> unique users
            during {{ $start->format('F Y') }}.
        </p>
    </section>

    {{-- SECTION 2: LEADS --}}
    <section class="report" id="section-2" style="position:relative">
        <a href="#manual-editor" style="position:absolute;top:14px;right:18px;font-size:11.5px;color:var(--muted);text-decoration:none;padding:4px 8px;border:1px solid var(--line);border-radius:4px">✏️ Edit</a>
        <h2>2. Leads</h2>
        <h3>{{ rtrim(rtrim(number_format($metrics['leads'] ?? 0, 2), '0'), '.') ?: '0' }} new leads this month</h3>
        <p class="intro">Leads are tracked as GA4 conversions (form submissions + phone-click events) and where applicable, call-tracking data.</p>
    </section>

    {{-- SECTION 3: SEO RANKINGS --}}
    <section class="report" id="section-3" style="position:relative">
        <a href="#manual-editor" style="position:absolute;top:14px;right:18px;font-size:11.5px;color:var(--muted);text-decoration:none;padding:4px 8px;border:1px solid var(--line);border-radius:4px">✏️ Edit keywords</a>
        <h2>3. SEO rankings · Progress</h2>
        <h3>Keyword performance</h3>

        @php
            $buckets = [
                'top3'        => 'Top 3 ranking keywords',
                'progressing' => 'Keywords that are also progressing',
                'long_tail'   => 'Long-tail keywords we track',
            ];
        @endphp

        @foreach ($buckets as $key => $label)
            <div class="kw-section">
                <h4>{{ $label }}</h4>
                <table class="kw">
                    <thead>
                        <tr><th style="width:55%">Keyword</th><th>Current</th><th>Previous</th><th>Change</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($rankings[$key] ?? [] as $kw)
                            <tr>
                                <td>{{ $kw->keyword }}</td>
                                <td class="pos">{{ $kw->position ?? '—' }}</td>
                                <td>{{ $kw->previous_position ?? '—' }}</td>
                                <td>
                                    @if ($kw->position && $kw->previous_position)
                                        @php $delta = $kw->previous_position - $kw->position; @endphp
                                        @if ($delta > 0)
                                            <span class="delta-up">▲ {{ $delta }}</span>
                                        @elseif ($delta < 0)
                                            <span class="delta-down">▼ {{ abs($delta) }}</span>
                                        @else
                                            <span class="delta-flat">— flat</span>
                                        @endif
                                    @else
                                        <span class="delta-flat">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td class="empty" colspan="4">No keywords recorded for this month.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach
    </section>

    {{-- SECTION 4: GMB --}}
    <section class="report" id="section-4">
        <h2>4. Google My Business · Performance</h2>
        <h3>How your profile is performing</h3>
        <div class="kpi-grid">
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">Profile impressions</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['gmb_impressions'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">Search + Maps · GBP</div>
            </div>
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">Website clicks from GMB</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['gmb_clicks'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">{{ $start->format('F Y') }} · GBP</div>
            </div>
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">Phone calls from GMB</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['gmb_calls'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">{{ $start->format('F Y') }} · GBP</div>
            </div>
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">Direction requests</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['gmb_directions'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">{{ $start->format('F Y') }} · GBP</div>
            </div>
        </div>
        <p class="intro">
            Your Business Profile was seen <strong>{{ rtrim(rtrim(number_format($metrics['gmb_impressions'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> times this month.
            <strong>{{ rtrim(rtrim(number_format($metrics['gmb_clicks'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> visitors
            clicked through to your website,
            <strong>{{ rtrim(rtrim(number_format($metrics['gmb_calls'] ?? 0, 2), '0'), '.') ?: '0' }}</strong>
            tapped the call button, and
            <strong>{{ rtrim(rtrim(number_format($metrics['gmb_directions'] ?? 0, 2), '0'), '.') ?: '0' }}</strong>
            asked for directions to your business.
        </p>
    </section>

    {{-- SECTION 5: AI SEARCH VISIBILITY --}}
    <section class="report" id="section-5">
        <h2>5. AI Search Visibility</h2>
        <h3>How your brand appears in AI-powered search</h3>
        <p class="intro">AI tools like ChatGPT, Perplexity, Google's AI Overviews and Microsoft Copilot now answer many search queries directly. This section tracks how often your site is cited as a source and how much traffic those tools refer to your website.</p>
        <div class="kpi-grid">
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">AI referral sessions</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['ai_referral_sessions'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">ChatGPT, Perplexity, Gemini, Copilot · GA4</div>
            </div>
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">AI referral users</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['ai_referral_users'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">{{ $start->format('F Y') }} · GA4</div>
            </div>
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">AI Overview appearances</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['ai_overview_appearances'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">Google AI Overviews · manual</div>
            </div>
            <div class="kpi">
                <a href="#manual-editor" class="edit" title="Edit manually">✏️ Edit</a>
                <div class="label">Brand mentions in AI answers</div>
                <div class="value">{{ rtrim(rtrim(number_format($metrics['ai_mentions'] ?? 0, 2), '0'), '.') ?: '—' }}</div>
                <div class="hint">Tracked queries · manual</div>
            </div>
        </div>
        <p class="intro">
            <strong>{{ rtrim(rtrim(number_format($metrics['ai_referral_sessions'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> sessions came from AI search tools this month, and your brand was cited <strong>{{ rtrim(rtrim(number_format($metrics['ai_mentions'] ?? 0, 2), '0'), '.') ?: '0' }}</strong> times in tracked AI answers. AI search is now a meaningful share of how customers discover services — we're optimising your content for LLM citations alongside traditional SEO.
        </p>
    </section>

    {{-- SECTION 6: WORK WE'VE DONE (auto from completed tasks, split by category) --}}
    <section class="report" id="section-6">
        <h2>6. Work we've done</h2>
        <h3>Completed in {{ $start->format('F Y') }}</h3>
        @php
            $contentDone = $doneTasks->where('category', 'content')->values();
            $otherDone   = $doneTasks->whereNotIn('category', ['content'])->values();
            // pull a URL out of the description if one was pasted in (so client can click through)
            $extractUrl = function ($text) {
                if (!$text) return null;
                if (preg_match('#https?://[^\s<>"\']+#', $text, $m)) return $m[0];
                return null;
            };
        @endphp

        @if ($contentDone->isNotEmpty())
            <h3 style="margin-top:18px">📝 Content published</h3>
            <p class="intro" style="margin:0 0 8px">Blog posts and content pages we wrote and published this month.</p>
            <ul class="tasks">
                @foreach ($contentDone as $t)
                    @php $url = $extractUrl($t->description); @endphp
                    <li>
                        <span>
                            <span class="check">✓</span> &nbsp;{{ $t->title }}
                            @if ($url) <a href="{{ $url }}" target="_blank" rel="noopener" style="font-size:11px;color:#2563eb;margin-left:8px">view ↗</a> @endif
                        </span>
                        <span class="when">{{ $t->updated_at->format('M j') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($otherDone->isNotEmpty())
            <h3 style="margin-top:18px">🛠 Other work delivered</h3>
            <ul class="tasks">
                @foreach ($otherDone as $t)
                    <li>
                        <span><span class="check">✓</span> &nbsp;{{ $t->title }}</span>
                        <span class="when">{{ $t->updated_at->format('M j') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($doneTasks->isEmpty())
            <p class="intro" style="color:var(--muted);font-style:italic">No tasks were marked Done in this period yet.</p>
        @endif
    </section>

    {{-- SECTION 7: WORK AHEAD (open tasks) --}}
    <section class="report" id="section-7">
        <h2>7. Work ahead</h2>
        <h3>Planned for the next month</h3>
        @if ($upcomingTasks->isEmpty())
            <p class="intro" style="color:var(--muted);font-style:italic">No upcoming tasks scheduled. Add tasks to the project to see them here.</p>
        @else
            <ul class="tasks">
                @foreach ($upcomingTasks as $t)
                    <li>
                        <span><span style="color:var(--c);font-weight:700">○</span> &nbsp;{{ $t->title }}</span>
                        <span class="when">{{ $t->end_date ? 'due ' . $t->end_date->format('M j') : 'no date' }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Thank you --}}
    <footer class="thanks">
        <h3>Thank you for your business</h3>
        <p>Let us know if you need our help.</p>
        @if ($project->business_phone)
            <p style="margin-top:8px"><strong>{{ $project->business_phone }}</strong></p>
        @endif
    </footer>

</div>
</body>
</html>
