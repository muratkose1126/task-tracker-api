<?php

use App\Models\Group;
use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;

test('workspace member can view group in public space', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('view', $group))->toBeTrue();
});

test('space member can view group in private space', function () {
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

    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceMember)->allows('view', $group))->toBeTrue();
});

test('non-space member cannot view group in private space', function () {
    $owner = User::factory()->create();
    $nonMember = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($nonMember)
        ->state(['role' => 'member'])
        ->create();

    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($nonMember)->allows('view', $group))->toBeFalse();
});

test('workspace owner can update group in public space', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('update', $group))->toBeTrue();
});

test('workspace admin can update group in public space', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($admin)
        ->admin()
        ->create();

    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($admin)->allows('update', $group))->toBeTrue();
});

test('workspace member cannot update group in public space', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('update', $group))->toBeFalse();
});

test('space admin can update group in private space', function () {
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

    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceAdmin)->allows('update', $group))->toBeTrue();
});

test('space editor can update group in private space', function () {
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

    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceEditor)->allows('update', $group))->toBeTrue();
});

test('space member cannot update group in private space', function () {
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

    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceMember)->allows('update', $group))->toBeFalse();
});

test('user cannot delete group if cannot update it', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('delete', $group))->toBeFalse();
});

test('workspace owner can delete group', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $group = Group::factory()->for($space)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('delete', $group))->toBeTrue();
});
