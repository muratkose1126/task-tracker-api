<?php

namespace Tests\Traits;

use App\Models\Group;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;

trait CreatesWorkspaceData
{
    protected function createWorkspaceWithOwner(?User $owner = null): Workspace
    {
        $owner ??= User::factory()->create();

        return Workspace::factory()->for($owner, 'owner')->create();
    }

    protected function createWorkspaceWithMember(
        ?User $owner = null,
        ?User $member = null,
        string $role = 'member'
    ): array {
        $owner ??= User::factory()->create();
        $member ??= User::factory()->create();
        $workspace = $this->createWorkspaceWithOwner($owner);

        $workspace->members()->attach($member, ['role' => $role]);

        return compact('workspace', 'owner', 'member');
    }

    protected function createSpaceWithParent(
        ?Workspace $workspace = null,
        string $visibility = 'public'
    ): Space {
        $workspace ??= $this->createWorkspaceWithOwner();

        return Space::factory()
            ->for($workspace)
            ->state(['visibility' => $visibility])
            ->create();
    }

    protected function createSpaceWithMembers(
        ?Workspace $workspace = null,
        ?User $user = null,
        string $role = 'admin'
    ): array {
        $workspace ??= $this->createWorkspaceWithOwner();
        $user ??= User::factory()->create();
        $space = $this->createSpaceWithParent($workspace);

        $space->members()->attach($user, ['role' => $role]);

        return compact('space', 'workspace', 'user');
    }

    protected function createGroupWithParent(?Space $space = null): Group
    {
        $space ??= $this->createSpaceWithParent();

        return Group::factory()->for($space)->create();
    }

    protected function createTaskListWithParent(
        ?Space $space = null,
        ?Group $group = null
    ): TaskList {
        $space ??= $this->createSpaceWithParent();

        return TaskList::factory()
            ->for($space)
            ->when($group, fn ($q) => $q->for($group))
            ->create();
    }

    protected function createTaskWithParent(
        ?TaskList $list = null,
        ?User $user = null
    ): Task {
        $list ??= $this->createTaskListWithParent();
        $user ??= User::factory()->create();

        return Task::factory()
            ->for($list)
            ->for($user)
            ->create();
    }
}
