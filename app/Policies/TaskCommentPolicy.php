<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\User;

class TaskCommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskComment $comment): bool
    {
        return $user->id === $comment->task->user_id || $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskComment $comment): bool
    {
        return $user->id === $comment->user_id; // Sadece comment sahibi
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskComment $comment): bool
    {
        return $user->id === $comment->task->user_id || $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskComment $comment): bool
    {
        return $user->id === $comment->task->user_id || $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskComment $comment): bool
    {
        return $user->id === $comment->task->user_id || $user->id === $comment->user_id;
    }
}
