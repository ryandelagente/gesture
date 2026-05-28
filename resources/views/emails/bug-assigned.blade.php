<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Bug assigned to you</title></head>
<body style="font-family:-apple-system,'Segoe UI',Roboto,sans-serif;background:#f8fafc;margin:0;padding:24px;color:#111827">
    <div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:28px">
        <h1 style="margin:0 0 12px;font-size:20px">Hi {{ $assignee->name }} 👋</h1>
        <p style="margin:0 0 14px;line-height:1.6">A bug has just been assigned to you in <strong>{{ $bug->project->title ?? 'a project' }}</strong>.</p>

        <div style="background:#f1f5f9;border-radius:6px;padding:14px 16px;margin:14px 0">
            <div style="font-weight:600;margin-bottom:6px">{{ $bug->title }}</div>
            <div style="font-size:13px;color:#6b7280">
                Priority: <strong>{{ ucfirst($bug->priority) }}</strong> ·
                Severity: <strong>{{ ucfirst($bug->severity) }}</strong>
                @if ($bug->bugStatus) · Status: <strong>{{ $bug->bugStatus->name }}</strong>@endif
            </div>
            @if ($bug->description)
                <div style="font-size:14px;color:#374151;margin-top:10px;white-space:pre-wrap">{{ \Illuminate\Support\Str::limit($bug->description, 240) }}</div>
            @endif
        </div>

        @if ($bug->source === 'widget' && $bug->page_url)
            <p style="margin:14px 0;font-size:13px;color:#6b7280">
                Reported via widget on <a href="{{ $bug->page_url }}" style="color:#2563eb">{{ $bug->page_url }}</a>
            </p>
        @endif

        <p style="margin:18px 0">
            <a href="{{ url('/bugs/' . $bug->id) }}" style="background:#2563eb;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px;font-size:14px;font-weight:500">Open the bug</a>
        </p>

        <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0">
        <p style="font-size:12px;color:#9ca3af;margin:0">Sent automatically by Gesture.</p>
    </div>
</body></html>
