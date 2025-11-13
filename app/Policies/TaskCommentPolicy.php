<?php

namespace App\Policies;

use App\Enums\ProjectRole;
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
        // Comment sahibi veya proje sahibi comment'i güncelleyebilir
        if ($user->id === $comment->user_id) {
            return true;
        }

        $project = $comment->task->project;
        if (! $project) {
            return false;
        }

        return $project->members()
            ->where('user_id', $user->id)
            ->where('role', ProjectRole::OWNER)
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskComment $comment): bool
    {
        // Task sahibi, comment sahibi veya proje sahibi silebilir
        if ($user->id === $comment->user_id) {
            return true;
        }

        if ($user->id === $comment->task->user_id) {
            return true;
        }

        $project = $comment->task->project;
        if (! $project) {
            return false;
        }

        return $project->members()
            ->where('user_id', $user->id)
            ->where('role', ProjectRole::OWNER)
            ->exists();
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
