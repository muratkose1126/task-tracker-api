<?php

use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;

test('workspace member can view task list in public space', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('view', $list))->toBeTrue();
});

test('space member can view task list in private space', function () {
    $owner = User::factory()->create();
    $spaceMember = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceMember)->allows('view', $list))->toBeTrue();
});

test('non-space member cannot view task list in private space', function () {
    $owner = User::factory()->create();
    $nonMember = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($nonMember)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($nonMember)->allows('view', $list))->toBeFalse();
});

test('workspace owner can update task list in public space', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('update', $list))->toBeTrue();
});

test('workspace admin can update task list in public space', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($admin)
        ->admin()
        ->create();

    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($admin)->allows('update', $list))->toBeTrue();
});

test('workspace member cannot update task list in public space', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('update', $list))->toBeFalse();
});

test('space admin can update task list in private space', function () {
    $owner = User::factory()->create();
    $spaceAdmin = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceAdmin)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceAdmin)
        ->admin()
        ->create();

    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceAdmin)->allows('update', $list))->toBeTrue();
});

test('space editor can update task list in private space', function () {
    $owner = User::factory()->create();
    $spaceEditor = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceEditor)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceEditor)
        ->state(['role' => 'editor'])
        ->create();

    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceEditor)->allows('update', $list))->toBeTrue();
});

test('space member cannot update task list in private space', function () {
    $owner = User::factory()->create();
    $spaceMember = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceMember)->allows('update', $list))->toBeFalse();
});

test('user cannot delete task list if cannot update it', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('delete', $list))->toBeFalse();
});

test('workspace owner can delete task list', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('delete', $list))->toBeTrue();
});
