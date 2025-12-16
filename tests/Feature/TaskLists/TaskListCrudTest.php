<?php

use App\Models\Group;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;

test('user can list task lists', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();
    TaskList::factory()->for($space)->count(3)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/spaces/{$space->id}/lists");

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKey('data');
});

test('user can create task list', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/spaces/{$space->id}/lists", [
            'name' => 'Backlog',
        ]);

    expect($response->status())->toBe(201);
    expect($response->json())->toHaveKey('data.id');
});

test('user can view task list', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();
    $list = TaskList::factory()->for($space)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/lists/{$list->id}");

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBe($list->id);
});

test('user can update task list', function () {
    $user = User::factory()->create();
    $workspace = $user->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $space = Space::factory()->for($workspace)->public()->create();
    $list = TaskList::factory()->for($space)->create(['name' => 'Original Name']);

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/lists/{$list->id}", [
            'name' => 'Updated Name',
        ]);

    expect($response->status())->toBe(200);
});

test('user can delete task list', function () {
    $user = User::factory()->create();
    $workspace = $user->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $space = Space::factory()->for($workspace)->public()->create();
    $list = TaskList::factory()->for($space)->create();

    $response = $this->actingAsApi($user)
        ->deleteJson("/api/v1/lists/{$list->id}");

    expect($response->status())->toBe(204);
});

test('list can be created without group', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/spaces/{$space->id}/lists", [
            'name' => 'No Group List',
        ]);

    expect($response->status())->toBe(201);
});

test('list can be attached to group', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();
    $group = Group::factory()->for($space)->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/spaces/{$space->id}/lists", [
            'name' => 'Group List',
            'group_id' => $group->id,
        ]);

    expect($response->status())->toBe(201);
});

test('list can be detached from group', function () {
    $user = User::factory()->create();
    $workspace = $user->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $space = Space::factory()->for($workspace)->public()->create();
    $group = Group::factory()->for($space)->create();
    $list = TaskList::factory()->for($group)->for($space)->create();

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/lists/{$list->id}", [
            'name' => $list->name,
            'group_id' => null,
        ]);

    expect($response->status())->toBe(200);
});

test('list name is required', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $workspace = $owner->workspaces()->create(['name' => 'Test', 'slug' => 'test']);
    $workspace->members()->attach($user, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->public()->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/spaces/{$space->id}/lists", []);

    expect($response->status())->toBe(422);
});

test('list can have multiple tasks', function () {
    $list = TaskList::factory()->create();
    Task::factory()->for($list)->count(5)->create();

    expect($list->tasks)->toHaveCount(5);
});

test('list can be archived', function () {
    $list = TaskList::factory()->archived()->create();

    expect($list->is_archived)->toBeTrue();
});

test('archived list hides tasks', function () {
    $list = TaskList::factory()->archived()->create();
    Task::factory()->for($list)->count(3)->create();

    expect($list->tasks)->toHaveCount(3);
});

test('deleting list removes tasks', function () {
    $user = User::factory()->create();
    $list = TaskList::factory()->create();
    $task = Task::factory()->for($list)->for($user)->create();

    $list->delete();

    // After deleting list, tasks should also be deleted
    expect(Task::where('list_id', $list->id)->count())->toBe(0);
});
