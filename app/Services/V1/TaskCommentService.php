<?php

namespace App\Services\V1;

use App\Models\Task;
use App\Models\TaskComment;

class TaskCommentService
{
    public function create(Task $task, array $data): TaskComment
    {
        return $task->comments()->create($data);
    }

    public function update(TaskComment $comment, array $data): TaskComment
    {
        $comment->update($data);

        return $comment;
    }

    public function delete(TaskComment $comment): void
    {
        $comment->delete();
    }
}
