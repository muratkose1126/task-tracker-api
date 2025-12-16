<?php

use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;

test('user cannot access other workspace', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $workspace1 = Workspace::factory()->for($user1, 'owner')->create();
    $workspace2 = Workspace::factory()->for($user2, 'owner')->create();

    $response = $this->actingAsApi($user1)
        ->getJson("/api/v1/workspaces/{$workspace2->id}");

    expect($response->status())->toBe(403);
});

test('non member cannot access private space', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $workspace = Workspace::factory()->for($user1, 'owner')->create();
    $space = Space::factory()->for($workspace)->private()->create();

    $response = $this->actingAsApi($user2)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(403);
});

test('non member cannot create task in space', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $workspace = Workspace::factory()->for($user1, 'owner')->create();
    $space = Space::factory()->for($workspace)->create();
    $list = TaskList::factory()->for($space)->create();

    $response = $this->actingAsApi($user2)
        ->postJson("/api/v1/lists/{$list->id}/tasks", [
            'title' => 'Unauthorized Task',
        ]);

    expect($response->status())->toBe(403);
});

test('non owner cannot delete task in private space', function () {
    $owner = User::factory()->create();
    $editor = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->private()->create();

    SpaceMember::factory()->for($space)->for($editor)->create(['role' => 'commenter']);

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($owner)->create();

    $response = $this->actingAsApi($editor)
        ->deleteJson("/api/v1/tasks/{$task->id}");

    expect($response->status())->toBe(403);
});

test('workspace owner can access any space', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $workspace->members()->attach($owner, ['role' => 'owner']);
    $space = Space::factory()->for($workspace)->private()->create();

    $response = $this->actingAsApi($owner)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(200);
});

test('space member can access space', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $workspace->members()->attach($member, ['role' => 'member']);
    $space = Space::factory()->for($workspace)->private()->create();
    $space->members()->attach($member, ['role' => 'editor']);

    $response = $this->actingAsApi($member)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(200);
});

test('commenter cannot update task', function () {
    $owner = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->private()->create();

    SpaceMember::factory()->for($space)->for($commenter)->create(['role' => 'commenter']);

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($owner)->create();

    $response = $this->actingAsApi($commenter)
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated',
        ]);

    expect($response->status())->toBe(403);
});

test('viewer cannot modify task', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();

    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $space = Space::factory()->for($workspace)->private()->create();

    SpaceMember::factory()->for($space)->for($viewer)->create(['role' => 'viewer']);

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($owner)->create();

    $response = $this->actingAsApi($viewer)
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated',
        ]);

    expect($response->status())->toBe(403);
});
