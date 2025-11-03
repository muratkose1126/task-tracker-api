<?php

namespace App\Models;

use App\Enums\TaskCommentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'type' => TaskCommentType::class,
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
