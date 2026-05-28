<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugWidgetRoute extends Model
{
    protected $fillable = [
        'project_id', 'url_pattern', 'assignee_id',
        'priority_override', 'sort_order', 'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public static function matchForUrl(int $projectId, ?string $url): ?self
    {
        if (!$url) return null;
        $rules = self::where('project_id', $projectId)
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($rules as $rule) {
            if (self::patternMatches($rule->url_pattern, $url)) {
                return $rule;
            }
        }
        return null;
    }

    public static function patternMatches(string $pattern, string $url): bool
    {
        $pattern = trim($pattern);
        if ($pattern === '' || $pattern === '*') return true;
        // Convert glob-ish pattern to regex: '*' → '.*', escape rest
        $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#i';
        return (bool) preg_match($regex, $url);
    }
}
