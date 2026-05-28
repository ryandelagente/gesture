<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Feedback update</title></head>
<body style="font-family:-apple-system,'Segoe UI',Roboto,sans-serif;background:#f8fafc;margin:0;padding:24px;color:#111827">
    <div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:28px">
        <h1 style="margin:0 0 12px;font-size:20px">Hi{{ $bug->guest_name ? ', ' . $bug->guest_name : '' }} 👋</h1>
        <p style="margin:0 0 14px;line-height:1.6">
            Thanks again for the feedback you sent us. Quick update — its status just changed:
        </p>
        <div style="background:#f1f5f9;border-radius:6px;padding:12px 16px;margin:14px 0">
            <div style="font-size:13px;color:#6b7280;margin-bottom:6px">{{ $bug->title }}</div>
            <div style="font-size:15px">
                <span style="text-decoration:line-through;color:#9ca3af">{{ $oldStatus }}</span>
                &nbsp;→&nbsp;
                <strong style="color:#2563eb">{{ $newStatus }}</strong>
            </div>
        </div>
        <p style="margin:14px 0;line-height:1.6;font-size:14px">
            We'll keep you posted as things progress. No reply needed — but if you'd like to add more detail, just reply to this email.
        </p>
        @if ($bug->page_url)
            <p style="margin:14px 0;font-size:13px;color:#6b7280">
                Original page: <a href="{{ $bug->page_url }}" style="color:#2563eb">{{ $bug->page_url }}</a>
            </p>
        @endif
        <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0">
        <p style="font-size:12px;color:#9ca3af;margin:0">Sent automatically by Gesture. You're receiving this because you submitted feedback via the on-page Feedback widget.</p>
    </div>
</body></html>
