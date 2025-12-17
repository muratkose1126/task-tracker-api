<?php

namespace App\Policies;

use App\Models\Space;
use App\Models\User;

class SpacePolicy
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
    public function view(User $user, Space $space): bool
    {
        // Must be workspace member first
        $workspace = $space->workspace;
        if ($workspace->owner_id !== $user->id && ! $workspace->members()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Workspace owner can always view any space
        if ($workspace->owner_id === $user->id) {
            return true;
        }

        // Public space: all workspace members can view
        if ($space->visibility === 'public') {
            return true;
        }

        // Private space: must be space member
        return $space->members()->where('user_id', $user->id)->exists();
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
    public function update(User $user, Space $space): bool
    {
        // Must be able to view first
        if (! $this->view($user, $space)) {
            return false;
        }

        // Public space: workspace owner or admin
        if ($space->visibility === 'public') {
            $workspace = $space->workspace;
            if ($workspace->owner_id === $user->id) {
                return true;
            }
            $member = $workspace->members()->where('user_id', $user->id)->first();

            return $member && in_array($member->pivot->role, ['admin']);
        }

        // Private space: space admin
        $spaceMember = $space->members()->where('user_id', $user->id)->first();

        return $spaceMember && in_array($spaceMember->pivot->role, ['admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Space $space): bool
    {
        // Same logic as update
        return $this->update($user, $space);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Space $space): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Space $space): bool
    {
        return false;
    }
}
