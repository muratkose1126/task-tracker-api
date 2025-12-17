<?php

namespace App\Policies;

use App\Models\TaskList;
use App\Models\User;

class TaskListPolicy
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
    public function view(User $user, TaskList $taskList): bool
    {
        // Delegate to SpacePolicy
        $spacePolicy = new SpacePolicy;

        return $spacePolicy->view($user, $taskList->space);
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
    public function update(User $user, TaskList $taskList): bool
    {
        // Must be able to view the space
        $spacePolicy = new SpacePolicy;
        if (! $spacePolicy->view($user, $taskList->space)) {
            return false;
        }

        $space = $taskList->space;

        // Public space: workspace owner or admin
        if ($space->visibility === 'public') {
            $workspace = $space->workspace;
            if ($workspace->owner_id === $user->id) {
                return true;
            }
            $member = $workspace->members()->where('user_id', $user->id)->first();

            return $member && in_array($member->pivot->role, ['admin']);
        }

        // Private space: space admin or editor
        $spaceMember = $space->members()->where('user_id', $user->id)->first();

        return $spaceMember && in_array($spaceMember->pivot->role, ['admin', 'editor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskList $taskList): bool
    {
        // Same logic as update
        return $this->update($user, $taskList);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskList $taskList): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskList $taskList): bool
    {
        return false;
    }
}
