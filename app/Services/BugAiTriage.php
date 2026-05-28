<?php

namespace App\Services;

use App\Models\Bug;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BugAiTriage
{
    public static function suggest(Bug $bug): ?array
    {
        $apiKey = config('openai.api_key');
        if (!$apiKey) {
            return null; // gracefully no-op without a key
        }

        $description = trim(($bug->title ?? '') . "\n\n" . ($bug->description ?? ''));
        if ($description === '') return null;

        $env = trim(($bug->browser ? "Browser: {$bug->browser}\n" : '')
              . ($bug->os ? "OS: {$bug->os}\n" : '')
              . ($bug->page_url ? "Page: {$bug->page_url}\n" : '')
              . (!empty($bug->js_errors) ? "JS errors present: " . count($bug->js_errors) . "\n" : ''));

        $prompt = <<<PROMPT
You are a bug triage assistant. Given a bug report, return a single JSON object with these keys:
- priority: one of "low", "medium", "high", "critical"
- severity: one of "minor", "major", "critical", "blocker"
- suggested_tags: an array of 0-5 short single-word lowercase tags (e.g. "checkout", "mobile", "accessibility", "data-loss")
- summary: a one-sentence rewrite of the bug suitable as the title (<= 80 chars)

Only return valid JSON, no markdown, no explanation.

Bug:
{$description}

{$env}
PROMPT;

        $model = config('openai.default_model', 'gpt-3.5-turbo');

        try {
            $resp = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $model,
                'temperature' => 0.2,
                'messages'    => [
                    ['role' => 'system', 'content' => 'You are a precise bug-triage assistant. Reply with JSON only.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            if (!$resp->successful()) {
                Log::warning('OpenAI triage non-2xx', ['status' => $resp->status(), 'body' => $resp->body()]);
                return null;
            }
            $content = data_get($resp->json(), 'choices.0.message.content');
            if (!$content) return null;

            $parsed = json_decode($content, true);
            if (!is_array($parsed)) return null;

            return [
                'priority'        => in_array($parsed['priority'] ?? null, ['low', 'medium', 'high', 'critical']) ? $parsed['priority'] : null,
                'severity'        => in_array($parsed['severity'] ?? null, ['minor', 'major', 'critical', 'blocker']) ? $parsed['severity'] : null,
                'suggested_tags'  => is_array($parsed['suggested_tags'] ?? null)
                    ? array_slice(array_values(array_filter(array_map(fn($t) => is_string($t) ? mb_strtolower(trim($t)) : null, $parsed['suggested_tags']))), 0, 5)
                    : [],
                'summary'         => is_string($parsed['summary'] ?? null) ? mb_substr($parsed['summary'], 0, 80) : null,
                'model'           => $model,
                'generated_at'    => now()->toIso8601String(),
            ];
        } catch (\Throwable $e) {
            Log::warning('OpenAI triage failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
