<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMetric extends Model
{
    protected $fillable = ['project_id', 'period_start', 'period_end', 'metric_key', 'metric_value', 'source'];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'metric_value' => 'decimal:2',
    ];

    public const KEYS = [
        'sessions'               => 'Website sessions',
        'users'                  => 'Total users',
        'page_views'             => 'Page views',
        'leads'                  => 'Total leads',
        'gmb_clicks'             => 'GMB website clicks',
        'gmb_calls'              => 'GMB phone calls',
        'gmb_directions'         => 'GMB direction requests',
        'gmb_impressions'        => 'GMB profile impressions',
        'ai_referral_sessions'   => 'AI referral sessions',
        'ai_referral_users'      => 'AI referral users',
        'ai_overview_appearances'=> 'AI Overview appearances',
        'ai_mentions'            => 'Brand mentions in AI answers',
    ];

    public const AI_SOURCE_HOSTS = [
        'chatgpt.com', 'chat.openai.com', 'openai.com',
        'perplexity.ai', 'www.perplexity.ai',
        'gemini.google.com', 'bard.google.com',
        'copilot.microsoft.com', 'bing.com/chat',
        'claude.ai', 'you.com',
        'duckduckgo.com/?q=&ia=chat',
    ];
}
