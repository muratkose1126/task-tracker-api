<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Space extends Model
{
    use HasFactory;

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

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'space_members', 'space_id', 'user_id')
            ->withPivot(['role'])
            ->withTimestamps();
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
