<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $project->title ?? 'Public board' }} — Feedback</title>
    <style>
        :root{--c:#2563eb;--bg:#f8fafc;--line:#e5e7eb;--muted:#6b7280;--ink:#111827}
        *{box-sizing:border-box}
        body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:var(--bg);color:var(--ink);line-height:1.55}
        header{background:#fff;border-bottom:1px solid var(--line);padding:18px 28px}
        header h1{margin:0;font-size:22px}
        header .meta{font-size:13px;color:var(--muted);margin-top:4px}
        .wrap{max-width:1200px;margin:24px auto;padding:0 18px}
        .toolbar{background:#fff;border:1px solid var(--line);border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
        .toolbar input{padding:6px 10px;border:1px solid var(--line);border-radius:5px;font-size:13px;min-width:240px}
        .toolbar .pill-list{display:flex;gap:6px;flex-wrap:wrap}
        .toolbar .pill-list button{padding:4px 10px;border-radius:9999px;border:1px solid var(--line);background:#fff;font-size:12px;cursor:pointer}
        .toolbar .pill-list button.active{background:var(--c);color:#fff;border-color:var(--c)}
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}
        .card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:14px;display:flex;flex-direction:column;gap:8px}
        .card .title{font-weight:600;font-size:14px}
        .card .meta{display:flex;flex-wrap:wrap;gap:6px;font-size:11.5px;color:var(--muted)}
        .pill{display:inline-block;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600}
        .priority-low{background:#dbeafe;color:#1e3a8a}
        .priority-medium{background:#fef3c7;color:#92400e}
        .priority-high{background:#fed7aa;color:#9a3412}
        .priority-critical{background:#fee2e2;color:#991b1b}
        .status-pill{background:#e5e7eb;color:#374151}
        .src-widget{background:#d1fae5;color:#065f46}
        .src-internal{background:#f3f4f6;color:#374151}
        .desc{font-size:12.5px;color:#374151;line-height:1.5}
        .thumb{margin-top:6px;border-radius:6px;overflow:hidden;border:1px solid var(--line)}
        .thumb img{display:block;width:100%}
        .empty{text-align:center;padding:60px 0;color:var(--muted)}
        footer{text-align:center;font-size:12px;color:var(--muted);padding:20px;margin-top:24px}
        a{color:var(--c);text-decoration:none}
    </style>
</head>
<body>
    <header>
        <h1>{{ $project->title ?? 'Project' }} — Feedback board</h1>
        <div class="meta">
            Read-only view · {{ $bugs->count() }} item(s) ·
            @if ($board->show_widget_only) Widget submissions only @else All bugs @endif
        </div>
    </header>

    <div class="wrap">
        <div class="toolbar">
            <input id="q" type="search" placeholder="Search…">
            <div class="pill-list" id="statusPills">
                <button data-status="" class="active">All</button>
                @foreach ($bugs->pluck('bugStatus.name')->filter()->unique()->values() as $s)
                    <button data-status="{{ $s }}">{{ $s }}</button>
                @endforeach
            </div>
        </div>

        @if ($bugs->isEmpty())
            <div class="empty">No feedback yet on this board.</div>
        @else
            <div class="grid" id="grid">
                @foreach ($bugs as $bug)
                    <div class="card" data-title="{{ strtolower($bug->title) }}" data-desc="{{ strtolower(\Illuminate\Support\Str::limit($bug->description ?? '', 300)) }}" data-status="{{ $bug->bugStatus->name ?? '' }}">
                        <div class="title">#{{ $bug->id }} — {{ $bug->title }}</div>
                        <div class="meta">
                            <span class="pill priority-{{ $bug->priority }}">{{ ucfirst($bug->priority) }}</span>
                            @if ($bug->bugStatus)
                                <span class="pill status-pill">{{ $bug->bugStatus->name }}</span>
                            @endif
                            <span class="pill src-{{ $bug->source === 'widget' ? 'widget' : 'internal' }}">{{ $bug->source === 'widget' ? 'widget' : 'internal' }}</span>
                            <span>{{ $bug->created_at?->diffForHumans() }}</span>
                        </div>
                        @if ($bug->description)
                            <div class="desc">{{ \Illuminate\Support\Str::limit($bug->description, 220) }}</div>
                        @endif
                        @if ($board->show_screenshots && $bug->screenshot_path)
                            <div class="thumb"><img src="{{ asset('storage/' . $bug->screenshot_path) }}" alt=""></div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <footer>Powered by Gesture · This is a read-only feedback board.</footer>

    <script>
        var q = document.getElementById('q');
        var grid = document.getElementById('grid');
        var pills = document.querySelectorAll('#statusPills button');
        var activeStatus = '';
        function applyFilters() {
            if (!grid) return;
            var qv = (q.value || '').toLowerCase();
            grid.querySelectorAll('.card').forEach(function (c) {
                var matchQ = !qv || c.dataset.title.indexOf(qv) >= 0 || c.dataset.desc.indexOf(qv) >= 0;
                var matchS = !activeStatus || c.dataset.status === activeStatus;
                c.style.display = (matchQ && matchS) ? '' : 'none';
            });
        }
        if (q) q.addEventListener('input', applyFilters);
        pills.forEach(function (b) {
            b.addEventListener('click', function () {
                activeStatus = b.dataset.status || '';
                pills.forEach(function (x) { x.classList.remove('active'); });
                b.classList.add('active');
                applyFilters();
            });
        });
    </script>
</body>
</html>
