<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskList extends Model
{
    protected $table = 'lists';

    protected $fillable = [
        'space_id',
        'group_id',
        'name',
        'status_schema',
        'is_archived',
    ];

    protected $casts = [
        'status_schema' => 'array',
        'is_archived' => 'boolean',
    ];

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'list_id');
    }
}
