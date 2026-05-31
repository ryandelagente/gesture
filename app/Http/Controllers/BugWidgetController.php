<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\BugStatus;
use App\Models\BugWidgetKey;
use App\Models\BugWidgetRoute;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BugWidgetController extends Controller
{
    public function preflight(Request $request): Response
    {
        return $this->cors(response('', 204), $request);
    }

    public function config(Request $request)
    {
        $publicKey = $request->query('widget_key') ?: $request->header('X-Widget-Key');
        if (!$publicKey) {
            return $this->cors(response()->json(['error' => 'missing widget_key'], 400), $request);
        }
        $key = BugWidgetKey::where('public_key', $publicKey)->where('is_enabled', true)->first();
        if (!$key) {
            return $this->cors(response()->json(['error' => 'invalid widget_key'], 403), $request);
        }
        if (!$key->isOriginAllowed($request->header('Origin'))) {
            return $this->cors(response()->json(['error' => 'origin not allowed'], 403), $request);
        }
        return $this->cors(response()->json([
            'brand_color'    => $key->brand_color,
            'brand_logo_url' => $key->brand_logo_url,
            'button_label'   => $key->button_label,
            'welcome_text'   => $key->welcome_text,
        ]), $request);
    }

    public function uploadVideo(Request $request)
    {
        $publicKey = $request->input('widget_key') ?: $request->header('X-Widget-Key');
        $bugId     = (int) $request->input('bug_id');
        if (!$publicKey || !$bugId) {
            return $this->cors(response()->json(['error' => 'missing widget_key or bug_id'], 400), $request);
        }
        $key = BugWidgetKey::where('public_key', $publicKey)->where('is_enabled', true)->first();
        if (!$key) {
            return $this->cors(response()->json(['error' => 'invalid widget_key'], 403), $request);
        }
        if (!$key->isOriginAllowed($request->header('Origin'))) {
            return $this->cors(response()->json(['error' => 'origin not allowed'], 403), $request);
        }

        $bug = Bug::where('id', $bugId)->where('project_id', $key->project_id)->first();
        if (!$bug) {
            return $this->cors(response()->json(['error' => 'bug not found'], 404), $request);
        }

        $request->validate([
            'video'      => 'required|file|mimetypes:video/webm,video/mp4|max:25600', // 25 MB
            'duration_s' => 'nullable|integer|min:1|max:60',
        ]);

        $file = $request->file('video');
        $ext = $file->getClientOriginalExtension() ?: 'webm';
        $relative = 'bug-widget-videos/' . $key->id . '/' . Str::uuid()->toString() . '.' . $ext;
        Storage::disk('public')->put($relative, file_get_contents($file->getRealPath()));

        $bug->forceFill([
            'video_path'       => $relative,
            'video_duration_s' => (int) $request->input('duration_s', 0),
        ])->save();

        return $this->cors(response()->json(['ok' => true, 'video_path' => $relative]), $request);
    }

    public function list(Request $request)
    {
        $publicKey = $request->query('widget_key') ?: $request->header('X-Widget-Key');
        if (!$publicKey) {
            return $this->cors(response()->json(['error' => 'missing widget_key'], 400), $request);
        }
        $key = BugWidgetKey::where('public_key', $publicKey)->where('is_enabled', true)->first();
        if (!$key) {
            return $this->cors(response()->json(['error' => 'invalid widget_key'], 403), $request);
        }
        if (!$key->isOriginAllowed($request->header('Origin'))) {
            return $this->cors(response()->json(['error' => 'origin not allowed'], 403), $request);
        }

        $pageUrl = $request->query('page_url');
        $email   = $request->query('guest_email');

        $query = Bug::with(['bugStatus', 'comments.user'])
            ->where('project_id', $key->project_id)
            ->where('source', 'widget');

        if ($pageUrl) {
            $query->where('page_url', $pageUrl);
        }
        if ($email) {
            $query->where('guest_email', $email);
        }

        $bugs = $query->latest()->limit(20)->get()->map(function ($b) {
            return [
                'id'          => $b->id,
                'title'       => $b->title,
                'description' => $b->description,
                'status'      => $b->bugStatus->name ?? null,
                'priority'    => $b->priority,
                'pin_x'       => $b->pin_x,
                'pin_y'       => $b->pin_y,
                'viewport_w'  => $b->viewport_w,
                'viewport_h'  => $b->viewport_h,
                'guest_name'  => $b->guest_name,
                'created_at'  => $b->created_at?->toIso8601String(),
                'comments'    => $b->comments->map(fn($c) => [
                    'author'     => $c->user?->name ?? 'Team',
                    'body'       => $c->comment ?? '',
                    'created_at' => $c->created_at?->toIso8601String(),
                ])->values(),
            ];
        });

        return $this->cors(response()->json(['ok' => true, 'bugs' => $bugs]), $request);
    }

    public function submit(Request $request)
    {
        $publicKey = $request->input('widget_key') ?: $request->header('X-Widget-Key');
        if (!$publicKey) {
            return $this->cors(response()->json(['error' => 'missing widget_key'], 400), $request);
        }

        $key = BugWidgetKey::where('public_key', $publicKey)->where('is_enabled', true)->first();
        if (!$key) {
            return $this->cors(response()->json(['error' => 'invalid widget_key'], 403), $request);
        }

        $origin = $request->header('Origin');
        if (!$key->isOriginAllowed($origin)) {
            return $this->cors(response()->json(['error' => 'origin not allowed'], 403), $request);
        }

        $limiterKey = 'widget-feedback:' . $key->id . ':' . $request->ip();
        if (RateLimiter::tooManyAttempts($limiterKey, 20)) {
            return $this->cors(response()->json(['error' => 'rate limited'], 429), $request);
        }
        RateLimiter::hit($limiterKey, 60);

        // Honeypot: bots fill hidden fields
        if (!empty($request->input('hp_company')) || !empty($request->input('hp_url'))) {
            $key->increment('spam_blocked_count');
            $key->forceFill(['last_spam_at' => now()])->save();
            // Look like success so spammers don't retry
            return $this->cors(response()->json(['ok' => true, 'bug_id' => 0]), $request);
        }
        // Content heuristics
        $reason = $this->spamHeuristicReason((string) $request->input('description', ''));
        if ($reason) {
            $key->increment('spam_blocked_count');
            $key->forceFill(['last_spam_at' => now()])->save();
            return $this->cors(response()->json(['ok' => true, 'bug_id' => 0]), $request);
        }

        $data = $request->validate([
            'title'             => 'nullable|string|max:255',
            'description'       => 'required|string|max:5000',
            'priority'          => 'nullable|in:low,medium,high,critical',
            'severity'          => 'nullable|in:minor,major,critical,blocker',
            'page_url'          => 'nullable|string|max:2048',
            'element_selector'  => 'nullable|string|max:1024',
            'pin_x'             => 'nullable|integer|min:0|max:65535',
            'pin_y'             => 'nullable|integer|min:0|max:65535',
            'viewport_w'        => 'nullable|integer|min:0|max:65535',
            'viewport_h'        => 'nullable|integer|min:0|max:65535',
            'user_agent'        => 'nullable|string|max:1024',
            'browser'           => 'nullable|string|max:100',
            'os'                => 'nullable|string|max:100',
            'guest_name'        => 'nullable|string|max:100',
            'guest_email'       => 'nullable|email|max:150',
            'screenshot'        => 'nullable|string',
            'console_log'       => 'nullable|array|max:50',
            'console_log.*.level'   => 'nullable|in:log,info,warn,error,debug',
            'console_log.*.message' => 'nullable|string|max:2000',
            'console_log.*.at'      => 'nullable|integer',
            'js_errors'         => 'nullable|array|max:20',
            'js_errors.*.message' => 'nullable|string|max:2000',
            'js_errors.*.source'  => 'nullable|string|max:500',
            'js_errors.*.line'    => 'nullable|integer',
            'js_errors.*.col'     => 'nullable|integer',
            'js_errors.*.stack'   => 'nullable|string|max:5000',
            'js_errors.*.at'      => 'nullable|integer',
            'perf_metrics'        => 'nullable|array',
            'perf_metrics.lcp'    => 'nullable|numeric',
            'perf_metrics.cls'    => 'nullable|numeric',
            'perf_metrics.fid'    => 'nullable|numeric',
            'perf_metrics.fcp'    => 'nullable|numeric',
            'perf_metrics.ttfb'   => 'nullable|numeric',
            'perf_metrics.dom_load_ms' => 'nullable|integer',
            'perf_metrics.full_load_ms' => 'nullable|integer',
        ]);

        $project = $key->project()->with('workspace')->first();
        if (!$project) {
            return $this->cors(response()->json(['error' => 'project not found'], 404), $request);
        }

        $statusId = $key->default_status_id
            ?: optional(BugStatus::where('workspace_id', $project->workspace_id)->where('is_default', true)->first())->id
            ?: optional(BugStatus::where('workspace_id', $project->workspace_id)->orderBy('order')->first())->id;

        if (!$statusId) {
            return $this->cors(response()->json(['error' => 'no bug status configured'], 422), $request);
        }

        $screenshotPath = null;
        if (!empty($data['screenshot']) && str_starts_with($data['screenshot'], 'data:image/')) {
            $screenshotPath = $this->storeScreenshot($data['screenshot'], $key->id);
        }

        $title = $data['title'] ?: Str::limit($data['description'], 80);

        // Auto-assign by URL pattern
        $assignedTo = null;
        $priority = $data['priority'] ?? 'medium';
        $matchedRoute = BugWidgetRoute::matchForUrl($project->id, $data['page_url'] ?? null);
        if ($matchedRoute) {
            $assignedTo = $matchedRoute->assignee_id;
            if ($matchedRoute->priority_override) {
                $priority = $matchedRoute->priority_override;
            }
        }

        $bug = Bug::create([
            'project_id'        => $project->id,
            'bug_status_id'     => $statusId,
            'title'             => $title,
            'description'       => $data['description'],
            'priority'          => $priority,
            'severity'          => $data['severity'] ?? 'major',
            'assigned_to'       => $assignedTo,
            'environment'       => trim(($data['browser'] ?? '') . ' / ' . ($data['os'] ?? ''), ' /'),
            'page_url'          => $data['page_url'] ?? null,
            'element_selector'  => $data['element_selector'] ?? null,
            'pin_x'             => $data['pin_x'] ?? null,
            'pin_y'             => $data['pin_y'] ?? null,
            'viewport_w'        => $data['viewport_w'] ?? null,
            'viewport_h'        => $data['viewport_h'] ?? null,
            'user_agent'        => $data['user_agent'] ?? substr($request->userAgent() ?? '', 0, 1024),
            'browser'           => $data['browser'] ?? null,
            'os'                => $data['os'] ?? null,
            'screenshot_path'   => $screenshotPath,
            'guest_name'        => $data['guest_name'] ?? null,
            'guest_email'       => $data['guest_email'] ?? null,
            'reported_by'       => null,
            'source'            => 'widget',
            'console_log'       => $data['console_log'] ?? null,
            'js_errors'         => $data['js_errors'] ?? null,
            'perf_metrics'      => $data['perf_metrics'] ?? null,
        ]);

        $key->forceFill(['last_used_at' => now()])->save();

        return $this->cors(response()->json([
            'ok' => true,
            'bug_id' => $bug->id,
        ], 201), $request);
    }

    private function storeScreenshot(string $dataUri, int $keyId): ?string
    {
        if (!preg_match('#^data:image/(png|jpe?g);base64,(.+)$#i', $dataUri, $m)) {
            \Log::warning('Widget screenshot rejected: not a valid data URI', ['key_id' => $keyId, 'sample' => substr($dataUri, 0, 60)]);
            return null;
        }
        $ext = strtolower($m[1]) === 'jpg' ? 'jpeg' : strtolower($m[1]);
        $bytes = base64_decode($m[2], true);
        if ($bytes === false) {
            \Log::warning('Widget screenshot rejected: base64 decode failed', ['key_id' => $keyId]);
            return null;
        }
        // Allow up to 24 MB — annotated full-page screenshots commonly exceed
        // the previous 8 MB cap. The PHP-level post_max_size still bounds the
        // overall request, so this is the upper bound for what we'll accept.
        $maxBytes = 24 * 1024 * 1024;
        if (strlen($bytes) > $maxBytes) {
            \Log::warning('Widget screenshot rejected: too large', [
                'key_id'  => $keyId,
                'size_kb' => intval(strlen($bytes) / 1024),
                'max_kb'  => intval($maxBytes / 1024),
            ]);
            return null;
        }
        try {
            $relative = 'bug-widget-screenshots/' . $keyId . '/' . Str::uuid()->toString() . '.' . $ext;
            Storage::disk('public')->put($relative, $bytes);
            return $relative;
        } catch (\Throwable $e) {
            \Log::error('Widget screenshot save failed', ['key_id' => $keyId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function spamHeuristicReason(string $text): ?string
    {
        $text = mb_strtolower($text);
        if (mb_strlen($text) < 5) return 'too-short';

        // Excess URLs (3+ URLs is suspicious for a feedback form)
        $urlCount = preg_match_all('#https?://#', $text);
        if ($urlCount >= 3) return 'too-many-urls';

        // Common spam keywords
        $keywords = ['viagra', 'casino', 'crypto airdrop', 'seo backlink', 'cheap loan', 'rolex replica', 'porn', 'xanax', 'buy followers'];
        foreach ($keywords as $k) {
            if (str_contains($text, $k)) return 'keyword:' . $k;
        }

        // Excessive repetition (same character 8+ times)
        if (preg_match('/(.)\1{7,}/u', $text)) return 'repetition';

        return null;
    }

    private function cors($response, Request $request)
    {
        $origin = $request->header('Origin', '*');
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Vary', 'Origin');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Widget-Key');
        $response->headers->set('Access-Control-Max-Age', '600');
        return $response;
    }
}
