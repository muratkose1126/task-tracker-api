<?php

use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;

test('user can list tasks in list', function () {
    $user = User::factory()->create();
    $list = TaskList::factory()->create();
    Task::factory()->for($list)->for($user)->count(5)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/lists/{$list->id}/tasks");

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKey('data');
});

test('user can create task', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $workspace->members()->attach($owner, ['role' => 'owner']);
    $space = Space::factory()->for($workspace)->public()->create();
    $list = TaskList::factory()->for($space)->create();

    $response = $this->actingAsApi($owner)
        ->postJson("/api/v1/lists/{$list->id}/tasks", [
            'title' => 'Fix login bug',
            'description' => 'The login button is not working',
            'status' => 'todo',
            'priority' => 'high',
        ]);

    expect($response->status())->toBe(201);
    expect($response->json())->toHaveKey('data.id');
});

test('user can view task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/tasks/{$task->id}");

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBe($task->id);
});

test('user can update task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create([
        'title' => 'Original Title',
        'status' => 'todo',
    ]);

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'status' => 'in_progress',
        ]);

    expect($response->status())->toBe(200);
});

test('user can delete task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAsApi($user)
        ->deleteJson("/api/v1/tasks/{$task->id}");

    expect($response->status())->toBe(204);
});

test('task title is required', function () {
    $user = User::factory()->create();
    $list = TaskList::factory()->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/lists/{$list->id}/tasks", [
            'status' => 'todo',
        ]);

    expect($response->status())->toBe(422);
});

test('task can have status', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['status' => 'in_progress']);

    expect($task->status->value)->toBe('in_progress');
});

test('task can have priority', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['priority' => 'high']);

    expect($task->priority->value)->toBe('high');
});

test('unauthenticated user cannot create task', function () {
    $list = TaskList::factory()->create();

    $response = $this->postJson("/api/v1/lists/{$list->id}/tasks", [
        'title' => 'Test Task',
    ]);

    expect($response->status())->toBe(401);
});

test('deleted task is soft deleted', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $this->actingAsApi($user)
        ->deleteJson("/api/v1/tasks/{$task->id}");

    expect($task->fresh()->trashed())->toBeTrue();
});

test('soft deleted task not in list', function () {
    $user = User::factory()->create();
    $list = TaskList::factory()->create();
    $task = Task::factory()->for($list)->for($user)->create();

    $task->delete();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/lists/{$list->id}/tasks");

    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->not->toContain($task->id);
});
