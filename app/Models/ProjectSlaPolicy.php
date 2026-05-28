<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSlaPolicy extends Model
{
    protected $fillable = ['project_id', 'priority', 'respond_hours', 'resolve_hours'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public static function defaultsForPriority(string $priority): array
    {
        return match ($priority) {
            'critical' => ['respond_hours' => 1,  'resolve_hours' => 8],
            'high'     => ['respond_hours' => 4,  'resolve_hours' => 24],
            'medium'   => ['respond_hours' => 24, 'resolve_hours' => 72],
            'low'      => ['respond_hours' => 72, 'resolve_hours' => 240],
            default    => ['respond_hours' => 24, 'resolve_hours' => 72],
        };
    }

    public static function lookup(int $projectId, string $priority): array
    {
        $row = self::where('project_id', $projectId)->where('priority', $priority)->first();
        if ($row) {
            return ['respond_hours' => $row->respond_hours, 'resolve_hours' => $row->resolve_hours];
        }
        return self::defaultsForPriority($priority);
    }
}
