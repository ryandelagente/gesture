<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BugWidgetKey extends Model
{
    protected $fillable = [
        'project_id', 'created_by', 'name', 'public_key',
        'allowed_origins', 'is_enabled', 'default_status_id', 'last_used_at',
        'brand_color', 'brand_logo_url', 'button_label', 'welcome_text',
    ];

    protected $casts = [
        'allowed_origins' => 'array',
        'is_enabled' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $row) {
            if (empty($row->public_key)) {
                $row->public_key = 'bh_' . Str::random(36);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOriginAllowed(?string $origin): bool
    {
        $list = $this->allowed_origins ?? [];
        if (empty($list)) {
            return true;
        }
        if (!$origin) {
            return false;
        }
        foreach ($list as $allowed) {
            $allowed = trim($allowed);
            if ($allowed === '*' || strcasecmp(rtrim($allowed, '/'), rtrim($origin, '/')) === 0) {
                return true;
            }
        }
        return false;
    }
}
