<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectWebhook extends Model
{
    protected $fillable = [
        'project_id', 'created_by', 'name', 'target_url', 'platform',
        'events', 'is_enabled', 'last_sent_at', 'fail_count',
    ];

    protected $casts = [
        'events'       => 'array',
        'is_enabled'   => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function detectPlatform(): string
    {
        if ($this->platform && $this->platform !== 'auto') return $this->platform;
        $host = parse_url($this->target_url, PHP_URL_HOST) ?? '';
        if (str_contains($host, 'slack.com')) return 'slack';
        if (str_contains($host, 'office.com') || str_contains($host, 'microsoft.com') || str_contains($host, 'webhook.office')) return 'teams';
        if (str_contains($host, 'discord.com') || str_contains($host, 'discordapp.com')) return 'discord';
        return 'generic';
    }
}
