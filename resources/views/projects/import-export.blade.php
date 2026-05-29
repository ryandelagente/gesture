<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Projects — Import / Export</title>
    <style>
        body { font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:#f8fafc; color:#111827; margin:0; padding:32px; }
        .wrap { max-width: 760px; margin: 0 auto; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        .sub { color:#6b7280; font-size:13px; margin-bottom:24px; }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:22px 24px; margin-bottom:18px; }
        .card h2 { font-size:15px; margin:0 0 6px; }
        .card p { font-size:13px; color:#374151; margin:0 0 14px; line-height:1.5; }
        .btn { display:inline-block; background:#10b981; color:#fff; border:none; border-radius:8px; padding:10px 18px; font-size:14px; font-weight:600; text-decoration:none; cursor:pointer; }
        .btn.secondary { background:#111827; }
        input[type=file] { display:block; margin-bottom:14px; font-size:13px; }
        .flash { background:#ecfdf5; border:1px solid #10b981; color:#065f46; padding:12px 14px; border-radius:8px; font-size:13px; margin-bottom:18px; }
        .err { background:#fef2f2; border:1px solid #ef4444; color:#991b1b; padding:12px 14px; border-radius:8px; font-size:13px; margin-bottom:18px; }
        .meta { font-size:12px; color:#6b7280; }
        code { background:#f1f5f9; padding:1px 5px; border-radius:4px; font-size:12px; }
        a.back { font-size:13px; color:#2563eb; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrap">
    <a class="back" href="{{ url('/projects') }}">← Back to projects</a>
    <h1 style="margin-top:10px">Projects — Import / Export</h1>
    <div class="sub">Workspace: <strong>{{ $workspace->name }}</strong> · {{ $count }} project{{ $count === 1 ? '' : 's' }} currently</div>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="err">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <h2>Export projects (CSV)</h2>
        <p>Download all projects in this workspace as a CSV file. Site admin passwords are decrypted into the file so they import correctly into another environment.</p>
        <a class="btn" href="{{ route('projects.export') }}">⬇ Download CSV</a>
    </div>

    <div class="card">
        <h2>Import projects (CSV)</h2>
        <p>Upload a CSV (same columns as the export). Projects are created in <strong>this workspace</strong>; rows whose <code>title</code> already exists here are updated instead of duplicated. Admin passwords are re-encrypted with this site's key on import.</p>
        <form method="POST" action="{{ route('projects.import') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" accept=".csv,text/csv" required>
            <button type="submit" class="btn secondary">⬆ Import CSV</button>
        </form>
        <p class="meta" style="margin-top:14px">Columns: {{ implode(', ', \App\Http\Controllers\ProjectController::CSV_COLUMNS) }}</p>
    </div>
</div>
</body>
</html>
