<?php

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;

test('task comment belongs to task', function () {
    $task = Task::factory()->create();
    $comment = TaskComment::factory()->for($task)->create();
    expect($comment->task->is($task))->toBeTrue();
});

test('task comment belongs to user', function () {
    $user = User::factory()->create();
    $comment = TaskComment::factory()->for($user)->create();
    expect($comment->user->is($user))->toBeTrue();
});

test('task comment has comment text', function () {
    $comment = TaskComment::factory()->create(['comment' => 'This is a test comment']);
    expect($comment->comment)->toBe('This is a test comment');
});

test('task comment type can be note', function () {
    $comment = TaskComment::factory()->create(['type' => 'note']);
    expect($comment->type->value)->toBe('note');
});

test('task comment type can be update', function () {
    $comment = TaskComment::factory()->asUpdate()->create();
    expect($comment->type->value)->toBe('update');
});

test('task comment type can be reminder', function () {
    $comment = TaskComment::factory()->asReminder()->create();
    expect($comment->type->value)->toBe('reminder');
});

test('task comment can be soft deleted', function () {
    $comment = TaskComment::factory()->create();
    $commentId = $comment->id;
    $comment->delete();
    expect(TaskComment::query()->where('id', $commentId)->exists())->toBeFalse();
    expect(TaskComment::withTrashed()->where('id', $commentId)->exists())->toBeTrue();
});

test('task comment timestamps are set', function () {
    $comment = TaskComment::factory()->create();
    expect($comment->created_at)->not()->toBeNull();
    expect($comment->updated_at)->not()->toBeNull();
});

test('task comment type is not null', function () {
    $comment = TaskComment::factory()->create(['type' => 'note']);
    expect($comment->type)->not()->toBeNull();
});
