<?php

namespace App\Services;

use App\Models\Bug;
use App\Models\ProjectWebhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BugWebhookDispatcher
{
    public static function dispatch(string $event, Bug $bug, array $extra = []): void
    {
        $hooks = ProjectWebhook::where('project_id', $bug->project_id)
            ->where('is_enabled', true)
            ->get()
            ->filter(function (ProjectWebhook $h) use ($event) {
                $list = $h->events ?? ['bug.created', 'bug.assigned', 'bug.status_changed'];
                return in_array($event, $list, true);
            });

        foreach ($hooks as $hook) {
            try {
                $payload = self::buildPayload($hook->detectPlatform(), $event, $bug, $extra);
                $resp = Http::timeout(8)->post($hook->target_url, $payload);
                if ($resp->successful()) {
                    $hook->forceFill(['last_sent_at' => now(), 'fail_count' => 0])->save();
                } else {
                    $hook->increment('fail_count');
                    Log::warning('Webhook non-2xx', ['id' => $hook->id, 'code' => $resp->status()]);
                }
            } catch (\Throwable $e) {
                $hook->increment('fail_count');
                Log::warning('Webhook failed', ['id' => $hook->id, 'error' => $e->getMessage()]);
            }
        }
    }

    private static function buildPayload(string $platform, string $event, Bug $bug, array $extra): array
    {
        $title = match ($event) {
            'bug.created'         => '🐞 New bug submitted',
            'bug.assigned'        => '👤 Bug assigned',
            'bug.status_changed'  => '🔄 Bug status changed',
            default               => 'Bug event',
        };

        $url = url('/bugs/' . $bug->id);
        $widgetUrl = $bug->source === 'widget' ? url('/bugs/' . $bug->id . '/widget-data') : null;

        $bugUrl = $widgetUrl ?: $url;
        $description = mb_substr($bug->description ?? '', 0, 280);
        $meta = [
            "*Project:* " . ($bug->project->title ?? 'n/a'),
            "*Priority:* " . ucfirst($bug->priority),
            "*Severity:* " . ucfirst($bug->severity),
        ];
        if (!empty($extra['old_status']) && !empty($extra['new_status'])) {
            $meta[] = "*Status:* {$extra['old_status']} → {$extra['new_status']}";
        }
        if ($bug->page_url) {
            $meta[] = "*Page:* " . $bug->page_url;
        }
        if ($bug->guest_email) {
            $meta[] = "*Reporter:* " . ($bug->guest_name ? $bug->guest_name . ' <' . $bug->guest_email . '>' : $bug->guest_email);
        }

        $text = "*{$title}* — <{$bugUrl}|{$bug->title}>";

        return match ($platform) {
            'slack' => [
                'text'   => $text,
                'blocks' => [
                    ['type' => 'section', 'text' => ['type' => 'mrkdwn', 'text' => $text]],
                    ['type' => 'section', 'text' => ['type' => 'mrkdwn', 'text' => $description ?: '_no description_']],
                    ['type' => 'section', 'text' => ['type' => 'mrkdwn', 'text' => implode("\n", $meta)]],
                ],
            ],
            'discord' => [
                'content' => null,
                'embeds'  => [[
                    'title'       => "$title — {$bug->title}",
                    'url'         => $bugUrl,
                    'description' => $description,
                    'color'       => match ($event) { 'bug.created' => 0xef4444, 'bug.assigned' => 0x2563eb, 'bug.status_changed' => 0x10b981, default => 0x6366f1 },
                    'fields'      => array_values(array_filter([
                        ['name' => 'Project',  'value' => $bug->project->title ?? 'n/a', 'inline' => true],
                        ['name' => 'Priority', 'value' => ucfirst($bug->priority),       'inline' => true],
                        ['name' => 'Severity', 'value' => ucfirst($bug->severity),       'inline' => true],
                        !empty($extra['old_status']) ? ['name' => 'Status', 'value' => "{$extra['old_status']} → {$extra['new_status']}", 'inline' => false] : null,
                        $bug->page_url ? ['name' => 'Page', 'value' => $bug->page_url, 'inline' => false] : null,
                    ])),
                    'timestamp'   => optional($bug->updated_at ?? $bug->created_at)->toIso8601String(),
                ]],
            ],
            'teams' => [
                '@type'      => 'MessageCard',
                '@context'   => 'http://schema.org/extensions',
                'themeColor' => match ($event) { 'bug.created' => 'EF4444', 'bug.assigned' => '2563EB', 'bug.status_changed' => '10B981', default => '6366F1' },
                'summary'    => $title,
                'title'      => "$title — {$bug->title}",
                'text'       => $description,
                'sections'   => [[
                    'facts' => array_values(array_filter([
                        ['name' => 'Project',  'value' => $bug->project->title ?? 'n/a'],
                        ['name' => 'Priority', 'value' => ucfirst($bug->priority)],
                        ['name' => 'Severity', 'value' => ucfirst($bug->severity)],
                        !empty($extra['old_status']) ? ['name' => 'Status', 'value' => "{$extra['old_status']} → {$extra['new_status']}"] : null,
                        $bug->page_url ? ['name' => 'Page', 'value' => $bug->page_url] : null,
                    ])),
                ]],
                'potentialAction' => [[
                    '@type' => 'OpenUri',
                    'name'  => 'Open bug',
                    'targets' => [['os' => 'default', 'uri' => $bugUrl]],
                ]],
            ],
            default => [
                'event'    => $event,
                'bug_id'   => $bug->id,
                'title'    => $bug->title,
                'description' => $description,
                'project'  => $bug->project->title ?? null,
                'priority' => $bug->priority,
                'severity' => $bug->severity,
                'status'   => $bug->bugStatus->name ?? null,
                'page_url' => $bug->page_url,
                'source'   => $bug->source,
                'reporter' => [
                    'name'  => $bug->guest_name,
                    'email' => $bug->guest_email,
                ],
                'extra'    => $extra,
                'admin_url' => $bugUrl,
            ],
        };
    }
}
