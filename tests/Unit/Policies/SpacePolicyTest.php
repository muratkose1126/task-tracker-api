<?php

use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;

test('workspace owner can view any space', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('view', $space))->toBeTrue();
});

test('public space is visible to all workspace members', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('view', $space))->toBeTrue();
});

test('private space is only visible to space members', function () {
    $owner = User::factory()->create();
    $spaceMember = User::factory()->create();
    $nonMember = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'private'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($nonMember)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceMember)->allows('view', $space))->toBeTrue();
    expect(\Illuminate\Support\Facades\Gate::forUser($nonMember)->allows('view', $space))->toBeFalse();
});

test('non-workspace member cannot view any space', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($stranger)->allows('view', $space))->toBeFalse();
});

test('workspace owner can update public space', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('update', $space))->toBeTrue();
});

test('workspace admin can update public space', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($admin)
        ->admin()
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($admin)->allows('update', $space))->toBeTrue();
});

test('workspace member cannot update public space', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('update', $space))->toBeFalse();
});

test('space admin can update private space', function () {
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

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceAdmin)->allows('update', $space))->toBeTrue();
});

test('space member cannot update private space', function () {
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

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceMember)->allows('update', $space))->toBeFalse();
});

test('user cannot delete space if cannot update it', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('delete', $space))->toBeFalse();
});

test('workspace owner can delete space', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('delete', $space))->toBeTrue();
});
