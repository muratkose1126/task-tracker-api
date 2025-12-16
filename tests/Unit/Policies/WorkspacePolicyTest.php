<?php

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;

test('owner can view workspace', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('view', $workspace))->toBeTrue();
});

test('member can view workspace', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('view', $workspace))->toBeTrue();
});

test('admin can view workspace', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($admin)
        ->admin()
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($admin)->allows('view', $workspace))->toBeTrue();
});

test('non member cannot view workspace', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($stranger)->allows('view', $workspace))->toBeFalse();
});

test('owner can update workspace', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('update', $workspace))->toBeTrue();
});

test('admin can update workspace', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($admin)
        ->admin()
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($admin)->allows('update', $workspace))->toBeTrue();
});

test('member cannot update workspace', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('update', $workspace))->toBeFalse();
});

test('guest cannot update workspace', function () {
    $owner = User::factory()->create();
    $guest = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($guest)
        ->guest()
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($guest)->allows('update', $workspace))->toBeFalse();
});

test('only owner can delete workspace', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($owner)->allows('delete', $workspace))->toBeTrue();
});

test('admin cannot delete workspace', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($admin)
        ->admin()
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($admin)->allows('delete', $workspace))->toBeFalse();
});

test('member cannot delete workspace', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($member)
        ->state(['role' => 'member'])
        ->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($member)->allows('delete', $workspace))->toBeFalse();
});
