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
        // Comment sahibi veya task owner'ı güncelleyebilir
        if ($user->id === $comment->user_id) {
            return true;
        }

        // Task owner'ı güncelleyebilir
        if ($user->id === $comment->task->user_id) {
            return true;
        }

        // Space member'ları güncelleyebilir
        $list = $comment->task->list;
        if (! $list) {
            return false;
        }

        $space = $list->space;
        if (! $space) {
            return false;
        }

        $spaceMember = $space->members()
            ->where('user_id', $user->id)
            ->first();

        return $spaceMember && in_array($spaceMember->pivot->role, ['admin', 'editor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskComment $comment): bool
    {
        // Task sahibi veya comment sahibi silebilir
        if ($user->id === $comment->user_id || $user->id === $comment->task->user_id) {
            return true;
        }

        // Space member'ları silebilir
        $list = $comment->task->list;
        if (! $list) {
            return false;
        }

        $space = $list->space;
        if (! $space) {
            return false;
        }

        $spaceMember = $space->members()
            ->where('user_id', $user->id)
            ->first();

        return $spaceMember && in_array($spaceMember->pivot->role, ['admin']);
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
