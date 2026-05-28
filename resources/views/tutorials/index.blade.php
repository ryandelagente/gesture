<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Help &amp; Tutorials — Gesture</title>
    <style>
        :root{--c:#2563eb;--bg:#f8fafc;--line:#e5e7eb;--muted:#6b7280;--ink:#111827}
        *{box-sizing:border-box}
        body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:var(--bg);color:var(--ink);line-height:1.55}
        header{background:#fff;border-bottom:1px solid var(--line);padding:16px 28px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
        header h1{margin:0;font-size:20px}
        header .role{font-size:12px;color:var(--muted)}
        header .role b{color:var(--ink)}
        header a{color:var(--c);text-decoration:none;font-size:14px}
        .layout{display:grid;grid-template-columns:240px 1fr;gap:24px;max-width:1240px;margin:24px auto;padding:0 16px}
        @media(max-width:880px){.layout{grid-template-columns:1fr}}
        nav.toc{position:sticky;top:16px;align-self:start;background:#fff;border:1px solid var(--line);border-radius:8px;padding:14px}
        nav.toc h3{margin:0 0 8px;font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
        nav.toc ul{list-style:none;padding:0;margin:0}
        nav.toc li{margin:2px 0}
        nav.toc a{display:block;padding:6px 10px;border-radius:5px;color:var(--ink);text-decoration:none;font-size:13.5px}
        nav.toc a:hover{background:#f1f5f9;color:var(--c)}
        main{min-width:0}
        .intro{background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;border-radius:10px;padding:22px 24px;margin-bottom:24px}
        .intro h2{margin:0 0 6px;font-size:22px}
        .intro p{margin:0;opacity:.92;font-size:15px}
        .card{background:#fff;border:1px solid var(--line);border-radius:10px;margin-bottom:20px;overflow:hidden;scroll-margin-top:14px}
        .card-head{padding:14px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:12px;background:#fafbfc}
        .card-head .icon{width:34px;height:34px;border-radius:8px;background:#dbeafe;color:var(--c);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
        .card-head h2{margin:0;font-size:16px}
        .card-head p{margin:1px 0 0;font-size:12.5px;color:var(--muted)}
        .tabs{display:flex;gap:0;border-bottom:1px solid var(--line);background:#f8fafc}
        .tabs button{flex:0 0 auto;background:transparent;border:none;border-bottom:2px solid transparent;padding:10px 18px;cursor:pointer;font-size:13px;font-weight:600;color:var(--muted)}
        .tabs button.active{color:var(--c);border-bottom-color:var(--c);background:#fff}
        .panel{padding:16px 22px;display:none}
        .panel.active{display:block}
        .panel h3{margin:16px 0 6px;font-size:14px}
        .panel h3:first-child{margin-top:6px}
        .panel ol,.panel ul{padding-left:20px;margin:6px 0}
        .panel li{margin:4px 0;font-size:14px}
        .panel p{margin:6px 0;font-size:14px}
        code{background:#f1f5f9;padding:1px 6px;border-radius:4px;font-size:12.5px;font-family:ui-monospace,SFMono-Regular,monospace}
        pre{background:#0f172a;color:#e2e8f0;padding:12px 14px;border-radius:6px;overflow:auto;font-size:12.5px;font-family:ui-monospace,SFMono-Regular,monospace;margin:10px 0}
        .tip{background:#fef3c7;border:1px solid #fde68a;border-radius:6px;padding:10px 14px;font-size:13.5px;color:#78350f;margin:10px 0}
        .tip b{color:#92400e}
        .note{background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:10px 14px;font-size:13.5px;color:#1e3a8a;margin:10px 0}
        .danger{background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:10px 14px;font-size:13.5px;color:#991b1b;margin:10px 0}
        .role-only{display:inline-block;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:2px 8px;border-radius:9999px;font-weight:700}
        .role-admin{background:#dbeafe;color:#1e3a8a}
        .role-client{background:#dcfce7;color:#166534}
        .role-member{background:#fef3c7;color:#78350f}
        .pill-list{display:flex;flex-wrap:wrap;gap:6px;margin:6px 0}
        .pill-list span{background:#f1f5f9;padding:4px 10px;border-radius:9999px;font-size:12px}
        a.inline{color:var(--c);text-decoration:none;border-bottom:1px dashed var(--c)}
        a.inline:hover{border-bottom-style:solid}
        .url{font-size:12.5px;color:var(--muted);font-family:ui-monospace,monospace;background:#f8fafc;padding:6px 10px;border-radius:4px;display:inline-block;margin-top:4px}
    </style>
</head>
<body>
<header>
    <div>
        <h1>📚 Help &amp; Tutorials</h1>
        <div class="role">Signed in as <b>{{ $user->name }}</b> ({{ $user->type }})</div>
    </div>
    <a href="{{ url('/dashboard') }}">&larr; Back to dashboard</a>
</header>

<div class="layout">
    <nav class="toc">
        <h3>Core modules</h3>
        <ul>
            <li><a href="#getting-started">🚀 Getting started</a></li>
            <li><a href="#workspaces">🏢 Workspaces &amp; members</a></li>
            <li><a href="#projects">📁 Projects</a></li>
            <li><a href="#tasks">✅ Tasks &amp; kanban</a></li>
            <li><a href="#bugs">🐞 Bugs</a></li>
            <li><a href="#timesheets">⏱️ Timesheets</a></li>
            <li><a href="#budgets">💰 Budgets &amp; expenses</a></li>
            <li><a href="#invoices">🧾 Invoices</a></li>
        </ul>
        <h3 style="margin-top:14px">Visual feedback (BugHerd-style)</h3>
        <ul>
            <li><a href="#bug-widget">💬 Bug widget</a></li>
            <li><a href="#widget-keys">🔑 Widget keys</a></li>
            <li><a href="#auto-assign">🎯 Auto-assign rules</a></li>
            <li><a href="#webhooks">📡 Outgoing webhooks</a></li>
            <li><a href="#public-board">🌐 Public board</a></li>
            <li><a href="#sla">⏰ SLA &amp; due dates</a></li>
            <li><a href="#ai-triage">✨ AI triage</a></li>
            <li><a href="#retention">🗑️ Data retention</a></li>
            <li><a href="#white-label">🎨 White-label</a></li>
            <li><a href="#i18n">🌍 Multi-language</a></li>
        </ul>
        <h3 style="margin-top:14px">Admin &amp; ops</h3>
        <ul>
            <li><a href="#roles">🔐 Roles &amp; permissions</a></li>
            <li><a href="#plans">📊 Plans &amp; subscriptions</a></li>
            <li><a href="#settings">⚙️ Settings</a></li>
            <li><a href="#scheduler">⏱️ Background jobs</a></li>
        </ul>
    </nav>

    <main>
        <div class="intro">
            <h2>Welcome to Gesture</h2>
            <p>Step-by-step walkthroughs for every module — pick a role on each card to see what <b>admins</b>, <b>members</b>, and <b>clients</b> can do.</p>
            <p style="margin-top:8px;font-size:14px;opacity:.92">21 modules including the full <b>BugHerd-style visual feedback suite</b>: widget keys, auto-assign rules, webhooks, public boards, SLA, AI triage, GDPR retention, white-label, and multi-language.</p>
        </div>

        {{-- ============================================================== --}}
        {{-- 1. GETTING STARTED                                              --}}
        {{-- ============================================================== --}}
        <section id="getting-started" class="card">
            <div class="card-head">
                <div class="icon">🚀</div>
                <div>
                    <h2>Getting started</h2>
                    <p>First-time setup for a new account</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin</button><button data-t>Member</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>1. Create your account</h3>
                <ol>
                    <li>Go to <a class="inline" href="{{ url('/register') }}">{{ url('/register') }}</a> and sign up — you become the <b>company</b> owner.</li>
                    <li>A default <b>workspace</b> is auto-created for you.</li>
                    <li>Default settings (timezone, currency, language) are copied from the super-admin profile.</li>
                </ol>
                <h3>2. Brand your account</h3>
                <ol>
                    <li>Open <a class="inline" href="{{ url('/settings/system') }}">Settings → System</a>.</li>
                    <li>Upload light/dark logos, set your timezone &amp; date format.</li>
                    <li>Upload favicon and set footer text.</li>
                </ol>
                <h3>3. Invite your first members</h3>
                <ol>
                    <li>Open <a class="inline" href="{{ url('/workspaces') }}">Workspaces</a> → click your workspace.</li>
                    <li>Click <b>Invite member</b> → enter email &amp; role (manager / member / client).</li>
                    <li>They get an email with an accept-invite link.</li>
                </ol>
                <div class="tip"><b>Pro tip:</b> Set up your <a class="inline" href="#plans">plans</a> and <a class="inline" href="#settings">payment gateway</a> before going live so SaaS sign-ups can pay immediately.</div>
            </div>

            <div class="panel">
                <h3>1. Accept your invite</h3>
                <ol>
                    <li>Click the invite link in your email.</li>
                    <li>Sign in (or create a password if first time).</li>
                    <li>You land on the workspace dashboard.</li>
                </ol>
                <h3>2. Get oriented</h3>
                <ul>
                    <li><b>Projects</b> — see what you've been assigned to.</li>
                    <li><b>Tasks</b> — your work, filterable by project and stage.</li>
                    <li><b>Timesheets</b> — log time spent on tasks.</li>
                    <li><b>Bugs</b> — issues assigned to you or reported by you.</li>
                </ul>
            </div>

            <div class="panel">
                <h3>What you can see as a client</h3>
                <ul>
                    <li>The projects you've been granted access to (you don't see others).</li>
                    <li>Tasks and bugs <em>you</em> reported.</li>
                    <li>Invoices addressed to you, with one-click payment.</li>
                </ul>
                <div class="note">Clients can't edit project details, add team members, or see internal team chat.</div>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 2. WORKSPACES & MEMBERS                                         --}}
        {{-- ============================================================== --}}
        <section id="workspaces" class="card">
            <div class="card-head">
                <div class="icon">🏢</div>
                <div>
                    <h2>Workspaces &amp; members</h2>
                    <p>Multi-tenant separation for teams or clients</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin</button><button data-t>Member</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>What is a workspace?</h3>
                <p>A workspace is a sealed container for projects, tasks, bugs, timesheets, and bug statuses. Members in one workspace don't see another workspace's data.</p>
                <h3>Add a workspace</h3>
                <ol>
                    <li>Open <a class="inline" href="{{ url('/workspaces') }}">{{ url('/workspaces') }}</a>.</li>
                    <li>Click <b>New workspace</b> → name it (e.g. "Client A", "Internal team").</li>
                    <li>You're auto-added as <b>owner</b>.</li>
                </ol>
                <h3>Member roles inside a workspace</h3>
                <ul>
                    <li><b>Owner</b> — full control, billing.</li>
                    <li><b>Manager</b> — manage projects, members, bugs.</li>
                    <li><b>Member</b> — work on assigned tasks/bugs, log time.</li>
                    <li><b>Client</b> — read-only on assigned projects, can report bugs and pay invoices.</li>
                </ul>
                <h3>Invite members</h3>
                <ol>
                    <li>Open a workspace → <b>Members</b> tab → <b>Invite</b>.</li>
                    <li>Enter email + workspace role.</li>
                    <li>Invitation is sent — they accept at <code>/invitations/{token}</code>.</li>
                </ol>
                <h3>Switch workspaces</h3>
                <p>Click the workspace name in the top bar → pick another. Each user has a <code>current_workspace_id</code> that persists across sessions.</p>
                <div class="danger"><b>Deleting a workspace cascades.</b> Projects, tasks, bugs, budgets, invoices belonging to it are also deleted.</div>
            </div>

            <div class="panel">
                <h3>Working across workspaces</h3>
                <ul>
                    <li>You can belong to many workspaces.</li>
                    <li>Switch via the top-bar workspace selector.</li>
                    <li>Each workspace remembers its own filter state, kanban view, etc.</li>
                </ul>
                <div class="note">Your role can differ per workspace — manager in one, member in another.</div>
            </div>

            <div class="panel">
                <p>Clients usually belong to a single workspace — the one shared with their service provider.</p>
                <ul>
                    <li>You don't see "Members" or "Invitations" admin tools.</li>
                    <li>You can be granted access to specific projects only.</li>
                </ul>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 3. PROJECTS                                                     --}}
        {{-- ============================================================== --}}
        <section id="projects" class="card">
            <div class="card-head">
                <div class="icon">📁</div>
                <div>
                    <h2>Projects</h2>
                    <p>Containers for tasks, bugs, budgets, and invoices</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Member</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>Create a project</h3>
                <ol>
                    <li>Sidebar → <b>Projects</b> → <b>New project</b>.</li>
                    <li>Title, description, dates, priority, status (planning / active / on hold / completed / cancelled).</li>
                    <li>Estimated hours and budget (optional).</li>
                </ol>
                <h3>Assign people</h3>
                <ol>
                    <li>Open the project → <b>Members</b> tab → <b>Add member</b>.</li>
                    <li>Pick a workspace member; assign sub-role (manager / member).</li>
                    <li>For clients: <b>Clients</b> tab → <b>Assign client</b>.</li>
                </ol>
                <h3>Milestones, notes, activities</h3>
                <ul>
                    <li><b>Milestones</b> — deliverable checkpoints with due dates.</li>
                    <li><b>Notes</b> — pinned text snippets (meeting notes, decisions).</li>
                    <li><b>Activities</b> — auto-logged history of changes.</li>
                </ul>
                <h3>Quick links inside a project</h3>
                <div class="pill-list">
                    <span>Tasks</span><span>Bugs</span><span>Bug Widget</span><span>Expenses</span><span>Budget</span><span>Timesheets</span><span>Invoices</span><span>Attachments</span>
                </div>
                <div class="tip"><b>Pro tip:</b> Use <b>Project Templates</b> for repeating client work — duplicate a project to inherit task stages and milestones.</div>
            </div>

            <div class="panel">
                <h3>What you see</h3>
                <ul>
                    <li>Projects you're explicitly added to as member or manager.</li>
                    <li>Your assigned tasks, your reported bugs, your logged time.</li>
                </ul>
                <h3>Update progress</h3>
                <p>Move tasks across stages — the project's <code>progress</code> percentage recalculates automatically (admin can also press <b>Recalculate progress</b>).</p>
            </div>

            <div class="panel">
                <p>Clients see only projects they've been added to.</p>
                <ul>
                    <li>Read-only access to tasks &amp; milestones.</li>
                    <li>Can report bugs against the project.</li>
                    <li>Receive and pay invoices.</li>
                </ul>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 4. TASKS                                                        --}}
        {{-- ============================================================== --}}
        <section id="tasks" class="card">
            <div class="card-head">
                <div class="icon">✅</div>
                <div>
                    <h2>Tasks &amp; kanban</h2>
                    <p>Granular work items with checklists, comments, attachments</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Member</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>Stages = your workflow</h3>
                <ol>
                    <li>Open <b>Projects → Tasks → Manage stages</b>.</li>
                    <li>Default stages: <em>To Do, In Progress, Review, Done</em>. Drag to reorder, edit colours.</li>
                    <li>Stages are <b>per-workspace</b>, shared by all projects in it.</li>
                </ol>
                <h3>Create &amp; assign a task</h3>
                <ol>
                    <li>Inside a project → <b>Tasks</b> tab → <b>New task</b>.</li>
                    <li>Title, description, stage, priority, due date, estimated hours.</li>
                    <li>Assign one or many members.</li>
                </ol>
                <h3>Sub-features</h3>
                <ul>
                    <li><b>Checklists</b> — break a task into sub-items.</li>
                    <li><b>Comments</b> — threaded discussion.</li>
                    <li><b>Attachments</b> — upload files via the media library.</li>
                    <li><b>Subtasks</b> — nested tasks with their own status.</li>
                </ul>
                <h3>Views</h3>
                <p>List, table, and <b>Kanban</b> (drag &amp; drop between stages).</p>
            </div>

            <div class="panel">
                <h3>Daily flow</h3>
                <ol>
                    <li>Filter sidebar → <b>Assigned to me</b>.</li>
                    <li>Move tasks across the kanban as you work.</li>
                    <li>Tick checklist items to track sub-progress.</li>
                    <li>Start the timer to log time directly (see <a class="inline" href="#timesheets">Timesheets</a>).</li>
                </ol>
                <div class="tip"><b>Pro tip:</b> Use comments with <code>@mentions</code> to notify teammates.</div>
            </div>

            <div class="panel">
                <p>Clients see read-only task lists for their projects — useful for status visibility.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 5. BUGS                                                         --}}
        {{-- ============================================================== --}}
        <section id="bugs" class="card">
            <div class="card-head">
                <div class="icon">🐞</div>
                <div>
                    <h2>Bugs (internal tracker)</h2>
                    <p>Issue tracking with priority, severity, repro steps</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Member</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>Configure bug statuses</h3>
                <p>Each workspace has its own statuses (defaults: New, In Progress, Testing, Resolved, Closed). Edit at <a class="inline" href="{{ url('/bug-statuses') }}">/bug-statuses</a>.</p>
                <h3>Report a bug</h3>
                <ol>
                    <li>Sidebar → <b>Bugs</b> → <b>Report Bug</b>.</li>
                    <li>Pick project, status, milestone (optional).</li>
                    <li>Fill: title, description, priority, severity, steps to reproduce, expected vs actual behaviour, environment.</li>
                    <li>Attach screenshots/files; assign to a member.</li>
                </ol>
                <h3>Triage on the kanban</h3>
                <p>Drag bugs between statuses on the kanban view; assignees get notifications by email automatically when assigned or reassigned.</p>
                <h3>List view extras (new)</h3>
                <ul>
                    <li><b>Tags / labels</b> — colored pills per workspace; attach multiple to a bug. Auto-created by AI triage when matching tags don't exist.</li>
                    <li><b>Saved searches</b> — save a filter combo (project + status + priority + severity + search) by name; load it with one click. Stored per browser in <code>localStorage</code>.</li>
                    <li><b>Bulk operations</b> — checkboxes on each row + select-all. Select multiple → blue sticky bar at top → change status / priority / assignee, or delete in one shot.</li>
                    <li><b>CSV export</b> — <b>Export CSV</b> button on the page header. Respects current filters; 18 columns including widget metadata + tags. UTF-8 BOM so Excel opens cleanly.</li>
                    <li><b>Per-row checkboxes</b> are only on the list view (not kanban) — switch view with the buttons next to the filter row.</li>
                </ul>
                <h3>Bugs sourced from the widget</h3>
                <p>Bugs created via the visual feedback widget have <code>source=widget</code> and a screenshot with a pin. View at <code>/bugs/{id}/widget-data</code>. See the <a class="inline" href="#bug-widget">Bug Widget</a> section.</p>
                <h3>SLA &amp; due dates</h3>
                <p>New bugs get a <code>due_at</code> stamp based on the project's <a class="inline" href="#sla">SLA policy</a>. Overdue bugs show a red badge. <code>resolved_at</code> is set automatically when status changes to Resolved/Closed/Done.</p>
                <div class="tip"><b>Pro tip:</b> Use <em>severity</em> for technical impact, <em>priority</em> for business urgency. Don't conflate them.</div>
            </div>

            <div class="panel">
                <h3>Your daily bug view</h3>
                <ul>
                    <li>Filter by <b>Assigned to me</b> — bugs you own.</li>
                    <li>Update status as you investigate.</li>
                    <li>Add comments with logs, screenshots, repro details.</li>
                    <li>Set yourself as <b>resolved_by</b> when fixed.</li>
                </ul>
            </div>

            <div class="panel">
                <h3>Report bugs as a client</h3>
                <ol>
                    <li>Open a project you have access to.</li>
                    <li><b>Bugs</b> tab → <b>Report Bug</b>.</li>
                    <li>You see only bugs <em>you</em> reported (privacy by default).</li>
                </ol>
                <div class="note">If your team installed the <a class="inline" href="#bug-widget">Bug Widget</a> on the live site, you can also send feedback without logging in — see the next section.</div>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6. BUG WIDGET (overview)                                        --}}
        {{-- ============================================================== --}}
        <section id="bug-widget" class="card">
            <div class="card-head">
                <div class="icon">💬</div>
                <div>
                    <h2>Bug Widget — overview</h2>
                    <p>BugHerd-style visual feedback widget with 29 client features</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Visitor (end user)</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>What the widget does</h3>
                <p>A 54&nbsp;KB JavaScript snippet you drop into any web page (your own site or a client's). Visitors see a floating <strong>Feedback</strong> button that lets them pin any element, capture a screenshot, annotate it, optionally record a short video, and submit — all without logging in. Submissions land as bugs in your Gesture project.</p>

                <h3>Three things on the page</h3>
                <ul>
                    <li><b>💬 Feedback button</b> (bottom-right by default) — opens pin mode</li>
                    <li><b>🔴 Record button</b> — only on browsers that support screen capture; records up to 30s</li>
                    <li><b>🕐 History button</b> — shows all feedback ever submitted on the current URL (visible to everyone with the widget)</li>
                </ul>

                <h3>Quick install</h3>
                <pre>&lt;script src="{{ url('/widget.js') }}" data-key="bh_xxx" async&gt;&lt;/script&gt;</pre>
                <p>The widget loads asynchronously, so it never blocks page rendering. Works on plain HTML, WordPress, React, Vue, Next.js — any site.</p>

                <h3>Companion modules</h3>
                <p>The widget is the entry point — these admin modules manage what happens when feedback arrives:</p>
                <ul>
                    <li><a class="inline" href="#widget-keys">Widget keys</a> — generate and configure keys, set allowed origins, brand colors</li>
                    <li><a class="inline" href="#auto-assign">Auto-assign rules</a> — route bugs to people by URL pattern</li>
                    <li><a class="inline" href="#webhooks">Webhooks</a> — push to Slack, Teams, Discord</li>
                    <li><a class="inline" href="#public-board">Public board</a> — magic-link client view of all bugs</li>
                    <li><a class="inline" href="#sla">SLA &amp; due dates</a> — response-time targets</li>
                    <li><a class="inline" href="#ai-triage">AI triage</a> — automatic priority/severity/tag suggestions</li>
                    <li><a class="inline" href="#retention">Data retention</a> — GDPR auto-delete</li>
                    <li><a class="inline" href="#white-label">White-label</a> — brand color, logo, button label, welcome text per key</li>
                    <li><a class="inline" href="#i18n">Multi-language</a> — auto-translated widget UI in 6 languages</li>
                </ul>
                <div class="tip"><b>Pro tip:</b> All admin controls for these sub-modules live on one page: <a class="inline" href="#widget-keys">/projects/{id}/widget-keys</a>.</div>
            </div>

            <div class="panel">
                <h3>What you see as a visitor</h3>
                <p>On the very first visit you'll see a welcome tooltip explaining how it works. After that it stays minimal:</p>
                <ol>
                    <li>Click the floating <strong>Feedback</strong> button (bottom-right by default).</li>
                    <li>Hint bar appears: <em>"🎯 Click on the part of the page you want to comment on"</em>. Cursor turns into a crosshair. Press Esc to cancel.</li>
                    <li>Click any element. A screenshot is auto-captured (with a pin drawn on the spot).</li>
                    <li><b>Annotation toolbar</b> appears — draw rectangles, arrows, or blur sensitive info before sending. Click <em>"Use this screenshot"</em>.</li>
                    <li>Form opens — write the description (required), optionally add your name/email, priority, severity. Click <strong>Send feedback</strong>.</li>
                    <li>If you want, click the <strong>🔴 Record</strong> button first to attach a 30-second screen recording.</li>
                    <li>To see previous feedback on this page, click the <strong>🕐</strong> button next to Feedback.</li>
                </ol>
                <h3>What's sent automatically</h3>
                <div class="pill-list">
                    <span>Page URL</span><span>CSS selector</span><span>Pin (x, y)</span><span>Viewport size</span><span>Screenshot</span><span>Browser</span><span>OS</span><span>User-Agent</span><span>Console log (last 50)</span><span>JS errors (last 20)</span><span>LCP/CLS/FID/FCP/TTFB</span>
                </div>
                <p>No account, no login required.</p>
                <div class="note">Mobile / tablet: touch and tap work the same as click. Widget auto-sizes the button bigger on touch devices.</div>
                <div class="note">Want to see the welcome tooltip again? Dev-tools → Console → <code>localStorage.removeItem('tbw-welcomed')</code> → refresh.</div>
            </div>

            <div class="panel">
                <p>If your service provider installed the Bug Widget on your live site, you can submit feedback by clicking the floating button — no account, no login. See the <b>Visitor</b> tab for the step-by-step.</p>
                <p>Submissions arrive instantly in the development team's bug list. They get notified in Slack/Teams (if configured) and you'll get auto-reply emails when status changes — if you left your email.</p>
                <p>You may also have access to a <a class="inline" href="#public-board">read-only public board</a> showing all feedback on your project — your provider will share the link.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6a. WIDGET KEYS                                                  --}}
        {{-- ============================================================== --}}
        <section id="widget-keys" class="card">
            <div class="card-head">
                <div class="icon">🔑</div>
                <div>
                    <h2>Widget keys &amp; install snippet</h2>
                    <p>One key per environment / per client site</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button></div>
            <div class="panel active">
                <h3>Generate a key</h3>
                <ol>
                    <li>Open a project → header → <b>Bug Widget</b> button → lands on <code>/projects/&#123;id&#125;/widget-keys</code>.</li>
                    <li><b>Name</b> the key (e.g. "Production site", "Staging").</li>
                    <li>Paste <b>allowed origins</b> one per line — exact URLs that may use this key. Use <code>*</code> for any origin (not recommended).</li>
                    <li>Optional: open <em>🎨 Branding</em> to set color/logo/button label/welcome text (see <a class="inline" href="#white-label">white-label</a>).</li>
                    <li>Click <b>Generate key</b>; it appears in the list as <code>bh_xxx</code>.</li>
                </ol>
                <h3>Install snippet</h3>
                <pre>&lt;script src="{{ url('/widget.js') }}" data-key="bh_xxx" async&gt;&lt;/script&gt;</pre>
                <p>Optional script attributes:</p>
                <ul>
                    <li><code>data-color="#16a34a"</code> — primary color (overrides per-key branding)</li>
                    <li><code>data-position="bottom-left"</code> — default is bottom-right</li>
                    <li><code>data-lang="es"</code> — force a language (otherwise auto-detected from browser)</li>
                </ul>
                <h3>Security model</h3>
                <ul>
                    <li><b>Origin allowlist</b> — requests from any other domain are rejected with 403.</li>
                    <li><b>Rate limit</b> — 20 submissions/minute per IP per key.</li>
                    <li><b>Spam filters</b> — honeypot fields + content heuristics (keyword list, URL count, repetition); silently dropped + counter incremented on the key row.</li>
                    <li><b>Screenshot size cap</b> — 8 MB; PNG/JPEG only.</li>
                    <li><b>Video size cap</b> — 25 MB; WebM/MP4 only.</li>
                </ul>
                <div class="tip"><b>Pro tip:</b> Create separate keys per environment so you can disable one without breaking others. <b>Disable</b> pauses incoming feedback; <b>Delete</b> stops the snippet permanently.</div>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6b. AUTO-ASSIGN RULES                                            --}}
        {{-- ============================================================== --}}
        <section id="auto-assign" class="card">
            <div class="card-head">
                <div class="icon">🎯</div>
                <div>
                    <h2>Auto-assign rules</h2>
                    <p>Route bugs by URL pattern → assignee + priority override</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button></div>
            <div class="panel active">
                <h3>How it works</h3>
                <p>Each rule has a URL pattern with <code>*</code> wildcards. When a widget submission arrives, rules are checked top-to-bottom by <b>sort order</b>. First match wins — sets the assignee and (optionally) overrides the reporter's priority.</p>
                <h3>Examples</h3>
                <ul>
                    <li><code>https://example.com/checkout/*</code> → assign to Sara, priority <em>critical</em></li>
                    <li><code>*staging.example.com*</code> → assign to QA team, priority <em>medium</em></li>
                    <li><code>*/admin/*</code> → assign to backend lead, priority <em>high</em></li>
                </ul>
                <h3>Add a rule</h3>
                <ol>
                    <li>Open <code>/projects/&#123;id&#125;/widget-keys</code>.</li>
                    <li>Scroll to <b>Auto-assign rules</b> card.</li>
                    <li>Fill: URL pattern, assignee, priority override, sort order. Click <b>Add rule</b>.</li>
                </ol>
                <div class="note">Lower sort order numbers run first. Default is 100 — set 10 for high-priority overrides, 200 for catch-alls.</div>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6c. WEBHOOKS                                                     --}}
        {{-- ============================================================== --}}
        <section id="webhooks" class="card">
            <div class="card-head">
                <div class="icon">📡</div>
                <div>
                    <h2>Outgoing webhooks</h2>
                    <p>Slack / Microsoft Teams / Discord / generic JSON</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button></div>
            <div class="panel active">
                <h3>Supported events</h3>
                <ul>
                    <li><code>bug.created</code> — new bug submitted (widget or internal)</li>
                    <li><code>bug.assigned</code> — assignee changed</li>
                    <li><code>bug.status_changed</code> — status changed; includes <code>old_status</code> + <code>new_status</code></li>
                </ul>
                <h3>Auto-detected platform</h3>
                <p>Paste any of these webhook URLs and the payload format adapts automatically:</p>
                <ul>
                    <li><b>Slack</b> — <code>https://hooks.slack.com/services/...</code> → rich Block Kit message</li>
                    <li><b>Teams</b> — <code>https://*.webhook.office.com/...</code> → MessageCard with facts</li>
                    <li><b>Discord</b> — <code>https://discord.com/api/webhooks/...</code> → embed with fields</li>
                    <li><b>Anything else</b> → generic JSON payload with all bug fields</li>
                </ul>
                <h3>Setup</h3>
                <ol>
                    <li>In your chat platform, create an incoming webhook for a specific channel; copy the URL.</li>
                    <li>Gesture: <code>/projects/&#123;id&#125;/widget-keys</code> → <b>Outgoing webhooks</b> card.</li>
                    <li>Label it, paste the URL, tick which events to send, click <b>Add webhook</b>.</li>
                    <li>Use the <b>Send test</b> button to push the latest bug at the URL.</li>
                </ol>
                <div class="tip"><b>Pro tip:</b> Disable a noisy hook with the <strong>Disable</strong> button without losing config. <strong>Fail count</strong> column shows webhooks that have been failing — investigate or delete.</div>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6d. PUBLIC BOARD                                                 --}}
        {{-- ============================================================== --}}
        <section id="public-board" class="card">
            <div class="card-head">
                <div class="icon">🌐</div>
                <div>
                    <h2>Public read-only board</h2>
                    <p>Magic-link URL clients can view without an account</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Client</button></div>
            <div class="panel active">
                <h3>Create a board</h3>
                <ol>
                    <li><code>/projects/&#123;id&#125;/widget-keys</code> → <b>Public read-only boards</b> card.</li>
                    <li>Name it (e.g. "Client X — production").</li>
                    <li>Toggle <b>Widget bugs only</b> to hide internal team-only bugs.</li>
                    <li>Toggle <b>Show screenshots</b> off if your bugs contain sensitive imagery.</li>
                    <li>Click <b>Create</b> — copy the share URL (<code>/board/pb_xxx</code>) and send to your client.</li>
                </ol>
                <h3>What the client sees</h3>
                <ul>
                    <li>Project title + total count</li>
                    <li>Card grid with title, description, status, priority, source, screenshot</li>
                    <li>Search box (filters titles + descriptions)</li>
                    <li>Status pills for one-click filtering</li>
                </ul>
                <h3>What the client doesn't see</h3>
                <ul>
                    <li>No edit / assign / delete actions</li>
                    <li>No internal comments</li>
                    <li>No member list / settings / billing</li>
                </ul>
                <div class="tip"><b>Pro tip:</b> Tokens are random 40-char strings — guess-proof. To revoke, click <b>Disable</b> (board still in your list, link 404s) or <b>Delete</b> (gone forever).</div>
            </div>
            <div class="panel">
                <p>If your provider shared a <code>/board/pb_...</code> URL with you, open it in any browser — no login required.</p>
                <p>You can search, filter by status, and see screenshots of every issue. To submit new feedback, use the floating <b>Feedback</b> button on the actual site (if installed).</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6e. SLA & DUE DATES                                              --}}
        {{-- ============================================================== --}}
        <section id="sla" class="card">
            <div class="card-head">
                <div class="icon">⏰</div>
                <div>
                    <h2>SLA &amp; due dates</h2>
                    <p>Response and resolution targets per priority</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Member</button></div>
            <div class="panel active">
                <h3>Defaults</h3>
                <table style="border-collapse:collapse;font-size:13.5px">
                    <thead><tr><th style="border-bottom:1px solid var(--line);padding:6px 12px;text-align:left">Priority</th><th style="border-bottom:1px solid var(--line);padding:6px 12px;text-align:left">Respond within</th><th style="border-bottom:1px solid var(--line);padding:6px 12px;text-align:left">Resolve within</th></tr></thead>
                    <tbody>
                        <tr><td style="padding:6px 12px">Critical</td><td style="padding:6px 12px">1h</td><td style="padding:6px 12px">8h</td></tr>
                        <tr><td style="padding:6px 12px">High</td><td style="padding:6px 12px">4h</td><td style="padding:6px 12px">24h</td></tr>
                        <tr><td style="padding:6px 12px">Medium</td><td style="padding:6px 12px">24h</td><td style="padding:6px 12px">72h</td></tr>
                        <tr><td style="padding:6px 12px">Low</td><td style="padding:6px 12px">72h</td><td style="padding:6px 12px">240h (10 days)</td></tr>
                    </tbody>
                </table>
                <h3>Override per project</h3>
                <ol>
                    <li><code>/projects/&#123;id&#125;/widget-keys</code> → <b>SLA targets</b> card.</li>
                    <li>Set respond/resolve hours per priority.</li>
                    <li>Click <b>Save SLA</b>.</li>
                </ol>
                <h3>How it works</h3>
                <ul>
                    <li>New bugs get a <code>due_at</code> stamp = <code>created_at + resolve_hours</code>.</li>
                    <li>Changing priority recomputes <code>due_at</code>.</li>
                    <li>Moving to Resolved/Closed/Done sets <code>resolved_at</code> automatically.</li>
                </ul>
                <h3>Visual indicators on bug detail</h3>
                <ul>
                    <li>🟢 <span class="pill" style="background:#dcfce7;color:#166534">resolved on time</span></li>
                    <li>🔵 <span class="pill" style="background:#dbeafe;color:#1e3a8a">due in 3 hours</span></li>
                    <li>🔴 <span class="pill" style="background:#fee2e2;color:#991b1b">⚠ overdue by 2 hours</span></li>
                </ul>
            </div>
            <div class="panel">
                <p>Open <code>/bugs</code> and filter by overdue — the SLA badge tells you which bugs need urgent attention. Move them through the kanban to Resolved/Closed/Done to stop the clock.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6f. AI TRIAGE                                                    --}}
        {{-- ============================================================== --}}
        <section id="ai-triage" class="card">
            <div class="card-head">
                <div class="icon">✨</div>
                <div>
                    <h2>AI triage suggestions</h2>
                    <p>GPT-powered priority / severity / tag / title suggestions</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button></div>
            <div class="panel active">
                <h3>Setup</h3>
                <ol>
                    <li>Get an OpenAI API key from <a class="inline" href="https://platform.openai.com/api-keys" target="_blank" rel="noopener">platform.openai.com</a>.</li>
                    <li>Add to <code>.env</code>: <code>OPENAI_API_KEY=sk-...</code></li>
                    <li>Optionally set the model: <code>OPENAI_DEFAULT_MODEL=gpt-3.5-turbo</code> (default) or <code>gpt-4o-mini</code></li>
                    <li>Run <code>php artisan config:clear</code>.</li>
                </ol>
                <h3>What it generates</h3>
                <p>Every widget-source bug auto-triggers a call to OpenAI. The model returns:</p>
                <ul>
                    <li><b>Priority</b> — low / medium / high / critical</li>
                    <li><b>Severity</b> — minor / major / critical / blocker</li>
                    <li><b>Suggested tags</b> — up to 5 single-word labels (e.g. <code>checkout</code>, <code>mobile</code>, <code>accessibility</code>)</li>
                    <li><b>Summary</b> — rewritten title (≤80 chars)</li>
                </ul>
                <h3>Apply suggestions</h3>
                <p>Open the bug at <code>/bugs/&#123;id&#125;/widget-data</code> → see the purple <em>✨ AI triage suggestions</em> card → click <b>Apply suggestions</b>. It updates priority, severity, title, and attaches the suggested tags (creating new tag rows as needed).</p>
                <div class="tip"><b>Cost:</b> ~$0.0002 per bug with gpt-3.5-turbo. Set a usage cap in your OpenAI dashboard for safety.</div>
                <div class="note">No API key configured? The service silently no-ops — bugs work normally, just without suggestions.</div>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6g. DATA RETENTION                                               --}}
        {{-- ============================================================== --}}
        <section id="retention" class="card">
            <div class="card-head">
                <div class="icon">🗑️</div>
                <div>
                    <h2>Data retention (GDPR)</h2>
                    <p>Per-project auto-delete of old bugs + their screenshots and videos</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button></div>
            <div class="panel active">
                <h3>Why</h3>
                <p>Some clients require all personal data be removed after a fixed window. Bugs from the widget can include names, emails, IP-adjacent info, and screenshots of forms with PII — so this is the GDPR-friendly default lever to use.</p>
                <h3>Configure per project</h3>
                <ol>
                    <li><code>/projects/&#123;id&#125;/widget-keys</code> → <b>Data retention (GDPR)</b> card.</li>
                    <li>Set <b>Retention days</b> (e.g. 90).</li>
                    <li>Tick <b>Apply only to widget-source bugs</b> to leave internal team bugs untouched.</li>
                    <li>Click <b>Save retention</b>. Leave the field blank to keep forever.</li>
                </ol>
                <h3>How it runs</h3>
                <ul>
                    <li>An artisan command <code>bugs:retention-cleanup</code> runs nightly at <b>03:00</b> via the Laravel scheduler.</li>
                    <li>Bugs past the cutoff are deleted from the database; their screenshot/video files are deleted from <code>storage/app/public/bug-widget-screenshots/</code> and <code>bug-widget-videos/</code>.</li>
                    <li>Test now without deleting anything: <code>php artisan bugs:retention-cleanup --dry-run</code></li>
                </ul>
                <div class="danger"><b>Heads up:</b> the scheduler must actually be running. See <a class="inline" href="#scheduler">background jobs</a> for the one-time setup.</div>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6h. WHITE-LABEL                                                  --}}
        {{-- ============================================================== --}}
        <section id="white-label" class="card">
            <div class="card-head">
                <div class="icon">🎨</div>
                <div>
                    <h2>White-label widget</h2>
                    <p>Brand colors, logo, button label, welcome text — per key</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button></div>
            <div class="panel active">
                <h3>Per-key branding</h3>
                <p>Each widget key can override the defaults so client A and client B see entirely different-looking buttons even when the script is the same.</p>
                <ol>
                    <li><code>/projects/&#123;id&#125;/widget-keys</code> → expand <b>🎨 Branding</b> under the create-key form.</li>
                    <li>Set:
                        <ul>
                            <li><b>Brand color</b> — hex, e.g. <code>#16a34a</code></li>
                            <li><b>Logo URL</b> — 32×32 recommended, replaces the chat icon on the FAB</li>
                            <li><b>Button label</b> — aria-label and tooltip (e.g. "Report a bug")</li>
                            <li><b>Welcome text</b> — overrides the first-visit tooltip body</li>
                        </ul>
                    </li>
                    <li>Click <b>Generate key</b>. The widget fetches these via <code>/api/widget/config</code> before rendering, so changes apply on the next page load on the target site.</li>
                </ol>
                <div class="tip"><b>Pro tip:</b> Host the logo on a CDN with permissive CORS so it loads fast. Cross-origin restrictions don't apply to images.</div>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 6i. MULTI-LANGUAGE                                               --}}
        {{-- ============================================================== --}}
        <section id="i18n" class="card">
            <div class="card-head">
                <div class="icon">🌍</div>
                <div>
                    <h2>Multi-language widget</h2>
                    <p>EN · ES · FR · DE · PT · JA — auto-detected</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Visitor</button></div>
            <div class="panel active">
                <h3>How detection works</h3>
                <ol>
                    <li>Widget reads <code>navigator.language</code> (e.g. <code>es-MX</code>) → takes the first 2 letters → looks up the language table.</li>
                    <li>Unrecognized language falls back to English.</li>
                    <li>Optional: force a language with <code>&lt;script ... data-lang="fr"&gt;</code> regardless of browser.</li>
                </ol>
                <h3>What gets translated</h3>
                <p>All visible widget strings: FAB tooltip, pin-mode hint, form labels &amp; placeholders, modal title, status messages, history panel, help panel, welcome tooltip, annotation toolbar.</p>
                <h3>Missing strings</h3>
                <p>Per-key fallback to English — never a missing-key placeholder shown to users.</p>
                <div class="note">Need a language not on the list? It's a 10-minute edit to <code>public/widget.js</code> — copy any block in the <code>I18N</code> object and translate the values.</div>
            </div>
            <div class="panel">
                <p>The widget should appear in your browser's language automatically. If it doesn't, your browser language might not be in the supported list (currently EN, ES, FR, DE, PT, JA) — fallback is English.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 7. TIMESHEETS                                                   --}}
        {{-- ============================================================== --}}
        <section id="timesheets" class="card">
            <div class="card-head">
                <div class="icon">⏱️</div>
                <div>
                    <h2>Timesheets</h2>
                    <p>Track time, submit for approval, generate reports</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Member</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>Approval flow</h3>
                <p>A timesheet has stages: <em>Draft → Submitted → Approved / Rejected</em>. Managers approve their team's submissions.</p>
                <h3>As manager: review</h3>
                <ol>
                    <li>Sidebar → <b>Timesheets → Approvals</b>.</li>
                    <li>Open a submitted sheet → review entries.</li>
                    <li>Approve or reject with a comment.</li>
                </ol>
                <h3>Reports</h3>
                <p>Use the dashboard widgets and <code>/timesheet-reports</code> for per-user / per-project totals, billable vs non-billable.</p>
            </div>

            <div class="panel">
                <h3>Two ways to log time</h3>
                <ol>
                    <li><b>Timer</b> — start/stop on a specific task. The timer keeps running in the background even if you change pages.</li>
                    <li><b>Manual entry</b> — fill in start/end times for past work.</li>
                </ol>
                <h3>Submit a timesheet</h3>
                <ol>
                    <li>Open <a class="inline" href="{{ url('/timesheets') }}">{{ url('/timesheets') }}</a>.</li>
                    <li>Pick the week → add entries.</li>
                    <li>Click <b>Submit for approval</b> when done.</li>
                </ol>
                <div class="tip"><b>Pro tip:</b> Add a quick description to each entry — your manager won't approve "8 hours" with no detail.</div>
            </div>

            <div class="panel">
                <p>Clients don't see timesheets unless billing is enabled and they're added as approver — in which case they get the manager view scoped to their projects.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 8. BUDGETS & EXPENSES                                           --}}
        {{-- ============================================================== --}}
        <section id="budgets" class="card">
            <div class="card-head">
                <div class="icon">💰</div>
                <div>
                    <h2>Budgets &amp; expenses</h2>
                    <p>Project budgets, expense submission, approval workflows</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Member</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>Set up a project budget</h3>
                <ol>
                    <li>Open a project → <b>Budget</b> action.</li>
                    <li>Total amount + currency.</li>
                    <li>Define <b>categories</b> (e.g. Travel, Software, Subcontractors) with sub-budgets.</li>
                </ol>
                <h3>Approval workflows</h3>
                <ol>
                    <li>Settings → <b>Expense workflows</b> → define approver chains by amount threshold.</li>
                    <li>Submitted expenses route to approvers automatically.</li>
                </ol>
                <h3>Budget revisions</h3>
                <p>Need more budget? Submit a revision (with reason). Approvers review and approve/reject — full audit trail.</p>
                <h3>Recurring expenses</h3>
                <p>Use <b>Expense recurring</b> for monthly subscriptions (e.g., a SaaS tool). They auto-create entries on the schedule.</p>
            </div>

            <div class="panel">
                <h3>Submit an expense</h3>
                <ol>
                    <li>Open a project → <b>Expenses</b> → <b>New expense</b>.</li>
                    <li>Pick category, amount, description.</li>
                    <li>Attach receipts (PDF/images).</li>
                    <li>Submit for approval.</li>
                </ol>
                <p>You'll get notified when it's approved or returned with comments.</p>
            </div>

            <div class="panel">
                <p>Clients don't see internal expenses. Approved expenses may roll up into the invoice they receive.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 9. INVOICES                                                     --}}
        {{-- ============================================================== --}}
        <section id="invoices" class="card">
            <div class="card-head">
                <div class="icon">🧾</div>
                <div>
                    <h2>Invoices</h2>
                    <p>Create, send, and collect payment from clients</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Admin / Manager</button><button data-t>Member</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>Create an invoice</h3>
                <ol>
                    <li>Sidebar → <b>Invoices</b> → <b>New invoice</b>.</li>
                    <li>Pick client, project, currency.</li>
                    <li>Add line items (description, qty, unit price, tax).</li>
                    <li>Set due date and notes.</li>
                </ol>
                <h3>Payment gateways</h3>
                <p>Gesture ships with 30+ gateways: Stripe, PayPal, Razorpay, Mercado Pago, PayStack, Flutterwave, Cashfree, Mollie, Iyzico, PayTabs, YooKassa, Authorize.Net, Skrill, CoinGate, plus many regional ones.</p>
                <p>Enable any of them in <a class="inline" href="{{ url('/settings/payment') }}">Settings → Payment</a> with your API keys.</p>
                <h3>Send to client</h3>
                <p>Email the invoice link → client pays online → status flips to <em>Paid</em> automatically via webhook.</p>
                <div class="tip"><b>Pro tip:</b> Connect at least one gateway before going live. Bank transfer is enabled by default (manual reconciliation).</div>
            </div>

            <div class="panel">
                <p>Members typically don't create invoices — they're created by the project manager or company owner. Members can view invoices for projects they're on.</p>
            </div>

            <div class="panel">
                <h3>Receiving an invoice</h3>
                <ol>
                    <li>You get an email with a link.</li>
                    <li>Click → see invoice details (line items, due date, total).</li>
                    <li>Click <b>Pay now</b> → choose a gateway.</li>
                    <li>Pay → instant confirmation; receipt is sent to your email.</li>
                </ol>
                <h3>Past invoices</h3>
                <p>Log in → <b>Invoices</b> in your sidebar → see all invoices issued to you with statuses.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 10. ROLES & PERMISSIONS                                         --}}
        {{-- ============================================================== --}}
        <section id="roles" class="card">
            <div class="card-head">
                <div class="icon">🔐</div>
                <div>
                    <h2>Roles &amp; permissions</h2>
                    <p>Fine-grained access control via Spatie</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Super admin</button><button data-t>Company owner</button><button data-t>Member / Client</button></div>

            <div class="panel active">
                <h3>Two layers</h3>
                <ol>
                    <li><b>System roles</b> — <em>superadmin, company, client, member</em>. Set on the user record.</li>
                    <li><b>Workspace roles</b> — <em>owner, manager, member, client</em>. Set per-workspace via <code>workspace_members</code>.</li>
                </ol>
                <h3>Custom roles</h3>
                <ol>
                    <li>Open <a class="inline" href="{{ url('/roles') }}">/roles</a>.</li>
                    <li>Create a role; tick the permissions it grants (project_view, bug_create, invoice_update, etc.).</li>
                    <li>Assign the role to a user.</li>
                </ol>
                <h3>Permission categories</h3>
                <div class="pill-list">
                    <span>project_*</span><span>task_*</span><span>bug_*</span><span>invoice_*</span><span>expense_*</span><span>budget_*</span><span>timesheet_*</span><span>workspace_*</span><span>user_*</span><span>plan_*</span>
                </div>
            </div>

            <div class="panel">
                <p>As company owner you can manage all roles inside your own workspaces, but you can't modify the super-admin role or system-wide settings.</p>
            </div>

            <div class="panel">
                <p>Your role determines what menu items appear in the sidebar. Items you can't access are hidden — you won't see broken links.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 11. PLANS (SUPER ADMIN)                                         --}}
        {{-- ============================================================== --}}
        <section id="plans" class="card">
            <div class="card-head">
                <div class="icon">📊</div>
                <div>
                    <h2>Plans &amp; subscriptions</h2>
                    <p>Pricing tiers for SaaS mode</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Super admin</button><button data-t>Company owner</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>Create a plan</h3>
                <ol>
                    <li>Sidebar → <b>Plans</b> → <b>New plan</b>.</li>
                    <li>Name, monthly price, yearly price (auto-discount if set).</li>
                    <li>Limits: max users, clients, managers, projects per workspace; workspace limit; storage (GB).</li>
                    <li>Toggle ChatGPT integration, trial days.</li>
                </ol>
                <h3>Make a plan the default</h3>
                <p>Tick <em>is_default</em> — new company sign-ups get assigned to it.</p>
                <h3>Coupons</h3>
                <p>Manage at <a class="inline" href="{{ url('/coupons') }}">/coupons</a> — percentage / fixed discount, expiry, usage cap.</p>
                <h3>Plan orders &amp; requests</h3>
                <ul>
                    <li><b>Plan orders</b> — successful purchases. Webhook updates the company's plan_id and expiry.</li>
                    <li><b>Plan requests</b> — manual upgrade requests (bank-transfer flow).</li>
                </ul>
            </div>

            <div class="panel">
                <h3>Upgrade your plan</h3>
                <ol>
                    <li>Sidebar → <b>Subscription</b> (or banner if your plan is expiring).</li>
                    <li>Pick a plan → choose monthly or yearly.</li>
                    <li>Pay with your preferred gateway.</li>
                </ol>
                <p>Limits update immediately; storage usage is shown in your dashboard.</p>
            </div>

            <div class="panel">
                <p>Plans don't apply to clients — they're between the SaaS provider and the company account.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 12. SETTINGS                                                    --}}
        {{-- ============================================================== --}}
        <section id="settings" class="card">
            <div class="card-head">
                <div class="icon">⚙️</div>
                <div>
                    <h2>Settings</h2>
                    <p>System, brand, payment, email, language</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Super admin</button><button data-t>Company owner</button><button data-t>Client</button></div>

            <div class="panel active">
                <h3>System settings</h3>
                <ul>
                    <li><a class="inline" href="{{ url('/settings/system') }}">/settings/system</a> — default language, date/time format, calendar start day, timezone, email verification, landing page toggle.</li>
                    <li><b>Brand</b> — logos (light/dark), favicon, title, footer text, theme colour, layout direction (LTR/RTL), theme mode.</li>
                </ul>
                <h3>Payment</h3>
                <p>Enable gateways one by one at <a class="inline" href="{{ url('/settings/payment') }}">/settings/payment</a>. Each has its own keys/secrets/test-mode toggle.</p>
                <h3>Email templates</h3>
                <p>At <a class="inline" href="{{ url('/email-templates') }}">/email-templates</a> — edit subject + body for invoices, invitations, plan expiry, etc. Translatable per language.</p>
                <h3>Currencies &amp; languages</h3>
                <ul>
                    <li><a class="inline" href="{{ url('/currencies') }}">/currencies</a> — manage currency list. Set a default.</li>
                    <li><a class="inline" href="{{ url('/languages') }}">/languages</a> — install/translate UI strings.</li>
                </ul>
            </div>

            <div class="panel">
                <h3>Company-level settings</h3>
                <p>Each company has its own copy of brand &amp; preference settings, isolated from other companies. Settings are stored per <code>user_id</code> (the company's owner) and per <code>workspace_id</code>.</p>
                <ul>
                    <li>Brand colours, logo (override the super-admin default).</li>
                    <li>Currency and language for your customers.</li>
                </ul>
            </div>

            <div class="panel">
                <p>Clients can edit their own profile (name, avatar, password) under <code>/settings/profile</code> but cannot change brand/system settings.</p>
            </div>
        </section>

        {{-- ============================================================== --}}
        {{-- 13. BACKGROUND JOBS / SCHEDULER                                  --}}
        {{-- ============================================================== --}}
        <section id="scheduler" class="card">
            <div class="card-head">
                <div class="icon">⏱️</div>
                <div>
                    <h2>Background jobs &amp; scheduler</h2>
                    <p>Required for retention cleanup, queued emails, periodic webhooks</p>
                </div>
            </div>
            <div class="tabs"><button class="active" data-t>Windows (XAMPP)</button><button data-t>Linux / cPanel</button></div>

            <div class="panel active">
                <h3>One-time setup (Windows Task Scheduler)</h3>
                <ol>
                    <li>Open <b>PowerShell as Administrator</b>.</li>
                    <li>Paste and run:
                        <pre>schtasks /Create /SC MINUTE /MO 1 /TN "Gesture\schedule-run" /TR "c:\xampp82\htdocs\task\gesture-schedule.bat" /RL HIGHEST /F</pre>
                    </li>
                    <li>Verify: <code>schtasks /Query /TN "Gesture\schedule-run"</code></li>
                    <li>Remove later: <code>schtasks /Delete /TN "Gesture\schedule-run" /F</code></li>
                </ol>
                <h3>What runs</h3>
                <ul>
                    <li>Laravel scheduler fires every minute. Most tasks no-op silently.</li>
                    <li><code>bugs:retention-cleanup</code> runs at <b>03:00 daily</b> — see <a class="inline" href="#retention">data retention</a>.</li>
                    <li>Log: <code>storage/logs/schedule.log</code></li>
                </ul>
                <div class="tip"><b>Pro tip:</b> If you don't see this scheduled task running, check Windows Event Viewer → Applications and Services Logs → Microsoft → Windows → TaskScheduler.</div>
            </div>

            <div class="panel">
                <h3>Cron on Linux / cPanel</h3>
                <p>Add a single cron entry:</p>
                <pre>* * * * * cd /path/to/task && php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</pre>
                <p>Replace <code>/path/to/task</code> with the actual install path. On cPanel use the Cron Jobs interface and paste the same line.</p>
                <h3>What runs</h3>
                <ul>
                    <li><code>bugs:retention-cleanup</code> daily at 03:00 server time.</li>
                </ul>
            </div>
        </section>

        <div class="intro" style="background:linear-gradient(135deg,#059669,#2563eb);margin-top:8px">
            <h2>That's the tour!</h2>
            <p>Got a question we didn't cover? Bookmark this page — it's at <code style="background:rgba(0,0,0,.2);color:#fff;padding:2px 8px">{{ url('/tutorials') }}</code>.</p>
            <p style="margin-top:8px;font-size:14px;opacity:.92">Updated with all the BugHerd-style modules: console capture, tags, auto-assign, webhooks, public boards, SLA, AI triage, retention, white-label, multi-language, video recording, bulk ops, saved searches, and more.</p>
        </div>
    </main>
</div>

<script>
    // Tabs
    document.querySelectorAll('.card').forEach(function (card) {
        var btns = card.querySelectorAll('.tabs button');
        var panels = card.querySelectorAll('.panel');
        btns.forEach(function (btn, i) {
            btn.addEventListener('click', function () {
                btns.forEach(function (b) { b.classList.remove('active'); });
                panels.forEach(function (p) { p.classList.remove('active'); });
                btn.classList.add('active');
                if (panels[i]) panels[i].classList.add('active');
            });
        });
    });

    // Smooth jump from TOC + initial hash
    (function () {
        var hash = location.hash.replace('#', '');
        if (hash) {
            var t = document.getElementById(hash);
            if (t) setTimeout(function () { t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 50);
        }
    })();
</script>
</body>
</html>
