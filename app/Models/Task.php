<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Task extends Model implements HasMedia
{
    use HasFactory, SoftDeletes;
    use InteractsWithMedia;
    use LogsActivity;

    protected $fillable = [
        'list_id',
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
        'due_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('task')
            ->logOnly(['title', 'description', 'status', 'priority', 'due_date'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => $eventName);
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }

    public function taskList(): BelongsTo
    {
        return $this->list();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}
