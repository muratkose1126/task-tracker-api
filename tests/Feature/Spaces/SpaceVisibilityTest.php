<?php

use App\Models\Space;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;

test('public space is visible to workspace members', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $workspace->members()->attach($member, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();

    $response = $this->actingAsApi($member)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(200);
});

test('private space is not visible to non members', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $workspace->members()->attach($stranger, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->private()->create();

    $response = $this->actingAsApi($stranger)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(403);
});

test('private space is visible to space members', function () {
    $owner = User::factory()->create();
    $spaceMember = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $workspace->members()->attach($owner, ['role' => 'owner']);
    $workspace->members()->attach($spaceMember, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->private()->create();
    $space->members()->attach($spaceMember, ['role' => 'editor']);

    // Verify attachment
    expect($space->members()->count())->toBe(1);

    $response = $this->actingAsApi($spaceMember)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(200);
});

test('owner can view any space in workspace', function () {
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($owner, ['role' => 'owner']);
    $space = Space::factory()->for($workspace)->private()->create();

    $response = $this->actingAsApi($owner)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(200);
});

test('space visibility can be changed', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $space = Space::factory()->for($workspace)->public()->create();

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/spaces/{$space->id}", [
            'name' => $space->name,
            'visibility' => 'private',
        ]);

    expect($response->status())->toBe(200);
});

test('archived space can be viewed by owner', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $space = Space::factory()->for($workspace)->archived()->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(200);
});

test('non workspace member cannot access public space', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->public()->create();

    $response = $this->actingAsApi($stranger)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(403);
});

test('tasks in public space are visible to workspace members', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $workspace->members()->attach($member, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();
    $list = TaskList::factory()->for($space)->create();

    $response = $this->actingAsApi($member)
        ->getJson("/api/v1/spaces/{$space->id}/tasks");

    expect($response->status())->toBe(200);
});
