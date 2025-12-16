<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Space extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'visibility',
        'color',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(SpaceMember::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function lists(): HasMany
    {
        return $this->hasMany(TaskList::class);
    }
}
