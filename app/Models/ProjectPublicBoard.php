<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProjectPublicBoard extends Model
{
    protected $fillable = [
        'project_id', 'created_by', 'name', 'share_token',
        'show_widget_only', 'show_screenshots', 'is_enabled', 'last_viewed_at',
    ];

    protected $casts = [
        'show_widget_only' => 'boolean',
        'show_screenshots' => 'boolean',
        'is_enabled'       => 'boolean',
        'last_viewed_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $row) {
            if (empty($row->share_token)) {
                $row->share_token = 'pb_' . Str::random(40);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
