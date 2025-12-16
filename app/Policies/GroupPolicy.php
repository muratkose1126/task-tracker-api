<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use App\Policies\SpacePolicy;
use Illuminate\Auth\Access\Response;

class GroupPolicy
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
    public function view(User $user, Group $group): bool
    {
        // Delegate to SpacePolicy
        $spacePolicy = new SpacePolicy();
        return $spacePolicy->view($user, $group->space);
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
    public function update(User $user, Group $group): bool
    {
        // Must be able to view the space
        $spacePolicy = new SpacePolicy();
        if (!$spacePolicy->view($user, $group->space)) {
            return false;
        }

        $space = $group->space;

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
    public function delete(User $user, Group $group): bool
    {
        // Same logic as update
        return $this->update($user, $group);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Group $group): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Group $group): bool
    {
        return false;
    }
}
