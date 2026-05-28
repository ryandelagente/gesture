<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BugTag extends Model
{
    protected $fillable = ['workspace_id', 'name', 'color', 'created_by'];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function bugs(): BelongsToMany
    {
        return $this->belongsToMany(Bug::class, 'bug_tag_bug');
    }
}
