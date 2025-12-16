<?php

use App\Models\Group;
use App\Models\Space;
use App\Models\TaskList;
use App\Models\User;

test('user can list groups in space', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();
    Group::factory()->for($space)->count(3)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/spaces/{$space->id}/groups");

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKey('data');
});

test('user can create group', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/spaces/{$space->id}/groups", [
            'name' => 'Development',
            'color' => '#FF5733',
        ]);

    expect($response->status())->toBe(201);
    expect($response->json())->toHaveKey('data.id');
});

test('user can view group', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();
    $group = Group::factory()->for($space)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/groups/{$group->id}");

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBe($group->id);
});

test('user can update group', function () {
    $user = User::factory()->create();
    $workspace = $user->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $space = Space::factory()->for($workspace)->public()->create();
    $group = Group::factory()->for($space)->create(['name' => 'Original Name']);

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/groups/{$group->id}", [
            'name' => 'Updated Name',
            'color' => '#0000FF',
        ]);

    expect($response->status())->toBe(200);
});

test('user can delete group', function () {
    $user = User::factory()->create();
    $workspace = $user->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $space = Space::factory()->for($workspace)->public()->create();
    $group = Group::factory()->for($space)->create();

    $response = $this->actingAsApi($user)
        ->deleteJson("/api/v1/groups/{$group->id}");

    expect($response->status())->toBe(204);
});

test('group name is required', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/spaces/{$space->id}/groups", [
            'color' => '#FF5733',
        ]);

    expect($response->status())->toBe(422);
});

test('group can have color', function () {
    $group = Group::factory()->create(['color' => '#FF5733']);

    expect($group->color)->toBe('#FF5733');
});

test('group color is optional', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/spaces/{$space->id}/groups", [
            'name' => 'Test Group',
        ]);

    expect($response->status())->toBe(201);
});

test('group can have multiple lists', function () {
    $group = Group::factory()->create();
    TaskList::factory()->for($group)->count(5)->create();

    expect($group->lists)->toHaveCount(5);
});

test('deleting group does not delete lists', function () {
    $group = Group::factory()->create();
    $list = TaskList::factory()->for($group)->create();

    $group->delete();

    expect($list->fresh()->group_id)->toBeNull();
});

test('unauthenticated user cannot create group', function () {
    $space = Space::factory()->create();

    $response = $this->postJson("/api/v1/spaces/{$space->id}/groups", [
        'name' => 'Test',
    ]);

    expect($response->status())->toBe(401);
});
