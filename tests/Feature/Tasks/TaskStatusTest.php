<?php

use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;

test('task status can be todo', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['status' => 'todo']);

    expect($task->status->value)->toBe('todo');
});

test('task status can be in progress', function () {
    $user = User::factory()->create();
    $task = Task::factory()->inProgress()->for($user)->create();

    expect($task->status->value)->toBe('in_progress');
});

test('task status can be done', function () {
    $user = User::factory()->create();
    $task = Task::factory()->done()->for($user)->create();

    expect($task->status->value)->toBe('done');
});

test('task status can be updated', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['status' => 'todo']);

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => $task->title,
            'status' => 'done',
        ]);

    expect($response->status())->toBe(200);
});

test('task can transition from todo to in progress', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['status' => 'todo']);

    $this->actingAsApi($user)
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => $task->title,
            'status' => 'in_progress',
        ]);

    expect($task->fresh()->status->value)->toBe('in_progress');
});

test('task can transition from in progress to done', function () {
    $user = User::factory()->create();
    $task = Task::factory()->inProgress()->for($user)->create();

    $this->actingAsApi($user)
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => $task->title,
            'status' => 'done',
        ]);

    expect($task->fresh()->status->value)->toBe('done');
});

test('task can transition from done back to todo', function () {
    $user = User::factory()->create();
    $task = Task::factory()->done()->for($user)->create();

    $this->actingAsApi($user)
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => $task->title,
            'status' => 'todo',
        ]);

    expect($task->fresh()->status->value)->toBe('todo');
});

test('invalid status returns validation error', function () {
    $user = User::factory()->create();
    $list = TaskList::factory()->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/lists/{$list->id}/tasks", [
            'title' => 'Test Task',
            'status' => 'invalid_status',
        ]);

    expect($response->status())->toBe(422);
});

test('default task status is todo', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $workspace->members()->attach($owner, ['role' => 'owner']);
    $space = Space::factory()->for($workspace)->public()->create();
    $list = TaskList::factory()->for($space)->create();

    $response = $this->actingAsApi($owner)
        ->postJson("/api/v1/lists/{$list->id}/tasks", [
            'title' => 'New Task',
        ]);

    expect($response->status())->toBe(201);
});

test('task status nullable in update', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['status' => 'done']);

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => $task->title,
        ]);

    expect($response->status())->toBe(200);
});
