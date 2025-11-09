<?php

use App\Models\User;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows the owner of the comment to update their own comment', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'comment' => 'Original comment',
    ]);

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/comments/{$comment->id}", [
            'comment' => 'Updated comment',
        ])
        ->assertStatus(200)
        ->assertJsonFragment([
            'comment' => 'Updated comment',
        ]);
});

it('denies other users from updating the comment', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $owner->id]);
    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $owner->id,
    ]);

    $this->actingAs($other, 'sanctum')
        ->putJson("/api/v1/comments/{$comment->id}", [
            'comment' => 'Hacked comment',
        ])
        ->assertStatus(403);
});

it('allows the owner of the comment to delete their own comment', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/comments/{$comment->id}")
        ->assertStatus(204);
});

it('denies other users from deleting the comment', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $owner->id]);
    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $owner->id,
    ]);

    $this->actingAs($other, 'sanctum')
        ->deleteJson("/api/v1/comments/{$comment->id}")
        ->assertStatus(403);
});
