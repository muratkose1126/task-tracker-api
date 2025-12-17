<?php

use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;

test('task owner can view own task', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($owner, 'user')->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('view', $task))->toBeTrue();
});

test('space member can view task in public space', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $taskOwner = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('view', $task))->toBeTrue();
});

test('non-owner cannot view task in private space without membership', function () {
    $owner = User::factory()->create();
    $nonMember = User::factory()->create();
    $taskOwner = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($nonMember)
        ->state(['role' => 'member'])
        ->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($taskOwner)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($nonMember)->allows('view', $task))->toBeFalse();
});

test('space member can view task in private space', function () {
    $owner = User::factory()->create();
    $spaceMember = User::factory()->create();
    $taskOwner = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($taskOwner)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($taskOwner)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceMember)->allows('view', $task))->toBeTrue();
});
