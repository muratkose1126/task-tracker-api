<?php

namespace App\Policies;

use App\Enums\ProjectRole;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
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
    public function view(User $user, Task $task): bool
    {
        // Owner of the task or any member of the related project can view
        if ($task->user_id === $user->id) {
            return true;
        }

        $project = $task->project;
        if (! $project) {
            return false;
        }

        return $project->members()->where('user_id', $user->id)->exists();
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
    public function update(User $user, Task $task): bool
    {
        // Task owner or project owner can update
        if ($task->user_id === $user->id) {
            return true;
        }

        $project = $task->project;
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
    public function delete(User $user, Task $task): bool
    {
        // Task owner or project owner can delete
        if ($task->user_id === $user->id) {
            return true;
        }

        $project = $task->project;
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
    public function restore(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }
}
