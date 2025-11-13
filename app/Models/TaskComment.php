<?php

namespace App\Models;

use App\Enums\TaskCommentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TaskComment extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
        'type',
    ];

    protected $casts = [
        'type' => TaskCommentType::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('task_comment')
            ->logOnly(['comment', 'type'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => $eventName);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
