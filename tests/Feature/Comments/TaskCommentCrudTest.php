<?php

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;

test('user can list task comments', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    TaskComment::factory()->for($task)->for($user)->count(3)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/tasks/{$task->id}/comments");

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKey('data');
});

test('user can create comment', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/tasks/{$task->id}/comments", [
            'user_id' => $user->id,
            'comment' => 'This is a test comment',
            'type' => 'note',
        ]);

    expect($response->status())->toBe(201);
    expect($response->json())->toHaveKey('data.id');
});

test('user can view comment', function () {
    $user = User::factory()->create();
    $comment = TaskComment::factory()->for($user)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/comments/{$comment->id}");

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBe($comment->id);
});

test('user can update own comment', function () {
    $user = User::factory()->create();
    $comment = TaskComment::factory()->for($user)->create([
        'comment' => 'Original comment',
    ]);

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/comments/{$comment->id}", [
            'comment' => 'Updated comment',
        ]);

    expect($response->status())->toBe(200);
});

test('user can delete own comment', function () {
    $user = User::factory()->create();
    $comment = TaskComment::factory()->for($user)->create();

    $response = $this->actingAsApi($user)
        ->deleteJson("/api/v1/comments/{$comment->id}");

    expect($response->status())->toBe(204);
});

test('comment text is required', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/tasks/{$task->id}/comments", [
            'user_id' => $user->id,
        ]);

    expect($response->status())->toBe(422);
});

test('comment can have different types', function () {
    $user = User::factory()->create();

    $noteComment = TaskComment::factory()->for($user)->create(['type' => 'note']);
    $updateComment = TaskComment::factory()->for($user)->create(['type' => 'update']);
    $reminderComment = TaskComment::factory()->for($user)->create(['type' => 'reminder']);

    expect($noteComment->type->value)->toBe('note');
    expect($updateComment->type->value)->toBe('update');
    expect($reminderComment->type->value)->toBe('reminder');
});

test('unauthenticated user cannot create comment', function () {
    $task = Task::factory()->create();

    $response = $this->postJson("/api/v1/tasks/{$task->id}/comments", [
        'user_id' => 1,
        'comment' => 'Test',
    ]);

    expect($response->status())->toBe(401);
});
