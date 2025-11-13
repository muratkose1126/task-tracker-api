<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list all task comments', function () {
    $task = Task::factory()->create();
    TaskComment::factory()->count(2)->create([
        'task_id' => $task->id,
        'user_id' => $task->user_id,
    ]);

    $response = $this->actingAs($task->user, 'sanctum')
        ->getJson("/api/v1/tasks/{$task->id}/comments");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

it('can create a new task comment', function () {
    $task = Task::factory()->create();
    $user = $task->user;

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/tasks/{$task->id}/comments", [
            'user_id' => $user->id,
            'comment' => 'This is a test comment',
            'type' => 'note',
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'comment' => 'This is a test comment',
                'type' => 'note',
            ],
        ]);

    $this->assertDatabaseHas('task_comments', [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'comment' => 'This is a test comment',
    ]);
});

it('cannot create a comment without content', function () {
    $task = Task::factory()->create();
    $user = $task->user;

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/tasks/{$task->id}/comments", [
            'user_id' => $user->id,
            'type' => 'note',
        ]);

    $response->assertStatus(422);
});

it('can show a task comment', function () {
    $task = Task::factory()->create();
    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $task->user_id,
        'comment' => 'This is a visible comment',
        'type' => 'note',
    ]);

    $response = $this->actingAs($task->user, 'sanctum')
        ->getJson("/api/v1/comments/{$comment->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $comment->id,
                'comment' => 'This is a visible comment',
                'type' => 'note',
            ],
        ]);
});

it('can update a task comment', function () {
    $task = Task::factory()->create();
    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $task->user_id,
        'comment' => 'Old comment',
    ]);

    $response = $this->actingAs($task->user, 'sanctum')
        ->putJson("/api/v1/comments/{$comment->id}", [
            'comment' => 'Updated comment',
            'type' => 'reminder',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $comment->id,
                'comment' => 'Updated comment',
                'type' => 'reminder',
            ],
        ]);

    $this->assertDatabaseHas('task_comments', [
        'id' => $comment->id,
        'comment' => 'Updated comment',
        'type' => 'reminder',
    ]);
});

it('can delete a task comment', function () {
    $task = Task::factory()->create();
    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $task->user_id,
    ]);

    $response = $this->actingAs($task->user, 'sanctum')
        ->deleteJson("/api/v1/comments/{$comment->id}");

    $response->assertStatus(204);
    $this->assertSoftDeleted('task_comments', ['id' => $comment->id]);
});
