<?php

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskList;
use App\Models\User;

test('task belongs to list', function () {
    $list = TaskList::factory()->create();
    $task = Task::factory()->forList($list)->create();
    expect($task->list->is($list))->toBeTrue();
});

test('task belongs to user', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    expect($task->user->is($user))->toBeTrue();
});

test('task has many comments', function () {
    $task = Task::factory()->create();
    TaskComment::factory()->for($task)->count(3)->create();
    expect($task->comments)->toHaveCount(3);
});

test('task has title', function () {
    $task = Task::factory()->create(['title' => 'Fix bug']);
    expect($task->title)->toBe('Fix bug');
});

test('task has description', function () {
    $task = Task::factory()->create(['description' => 'Fix the login issue']);
    expect($task->description)->toBe('Fix the login issue');
});

test('task can be soft deleted', function () {
    $task = Task::factory()->create();
    $taskId = $task->id;
    $task->delete();
    expect(Task::query()->where('id', $taskId)->exists())->toBeFalse();
    expect(Task::withTrashed()->where('id', $taskId)->exists())->toBeTrue();
});

test('task status can be todo', function () {
    $task = Task::factory()->create(['status' => 'todo']);
    expect($task->status->value)->toBe('todo');
});

test('task status can be in progress', function () {
    $task = Task::factory()->inProgress()->create();
    expect($task->status->value)->toBe('in_progress');
});

test('task status can be done', function () {
    $task = Task::factory()->done()->create();
    expect($task->status->value)->toBe('done');
});

test('task priority can be low', function () {
    $task = Task::factory()->create(['priority' => 'low']);
    expect($task->priority->value)->toBe('low');
});

test('task priority can be medium', function () {
    $task = Task::factory()->create(['priority' => 'medium']);
    expect($task->priority->value)->toBe('medium');
});

test('task priority can be high', function () {
    $task = Task::factory()->create(['priority' => 'high']);
    expect($task->priority->value)->toBe('high');
});

test('task has due date', function () {
    $dueDate = now()->addDay();
    $task = Task::factory()->create(['due_date' => $dueDate]);
    expect($task->due_date->toDateString())->toBe($dueDate->toDateString());
});

test('task timestamps are set', function () {
    $task = Task::factory()->create();
    expect($task->created_at)->not()->toBeNull();
    expect($task->updated_at)->not()->toBeNull();
});

test('task has media attachments', function () {
    $task = Task::factory()->create();
    expect($task->media)->not()->toBeNull();
});
