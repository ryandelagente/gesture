<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectKeywordRanking extends Model
{
    protected $fillable = [
        'project_id', 'period_start', 'keyword',
        'position', 'previous_position', 'bucket',
    ];

    protected $casts = [
        'period_start' => 'date',
    ];

    public const BUCKETS = [
        'top3'        => 'Top 3 ranking',
        'progressing' => 'Progressing',
        'long_tail'   => 'Long-tail',
    ];
}
