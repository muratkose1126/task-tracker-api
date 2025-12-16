<?php

namespace App\Policies;

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
        // Owner of the task can view
        if ($task->user_id === $user->id) {
            return true;
        }

        // Check space access via list
        $list = $task->list;
        if (! $list) {
            return false;
        }

        $space = $list->space;
        if (! $space) {
            return false;
        }

        $spacePolicy = new SpacePolicy;

        return $spacePolicy->view($user, $space);
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
        // Task owner can update
        if ($task->user_id === $user->id) {
            return true;
        }

        // Check space permissions
        $list = $task->list;
        if (! $list) {
            return false;
        }

        $space = $list->space;
        $spacePolicy = new SpacePolicy;

        if (! $spacePolicy->view($user, $space)) {
            return false;
        }

        // Public space: workspace owner or admin
        if ($space->visibility === 'public') {
            $workspace = $space->workspace;
            if ($workspace->owner_id === $user->id) {
                return true;
            }
            $member = $workspace->members()->where('user_id', $user->id)->first();

            return $member && in_array($member->role, ['admin']);
        }

        // Private space: space admin or editor
        $spaceMember = $space->members()->where('user_id', $user->id)->first();

        return $spaceMember && in_array($spaceMember->role, ['admin', 'editor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Same logic as update
        return $this->update($user, $task);
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
