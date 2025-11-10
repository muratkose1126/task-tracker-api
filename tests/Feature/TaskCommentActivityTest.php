<?php

use App\Models\User;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs task comment creation', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum');

    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'comment' => 'Test comment'
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => TaskComment::class,
        'subject_id' => $comment->id,
        'causer_id' => $user->id,
        'log_name' => 'task_comment',
        'description' => 'created'
    ]);
});

it('logs task comment updates', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'comment' => 'Initial comment'
    ]);

    $this->actingAs($user, 'sanctum');

    $comment->update(['comment' => 'Updated comment']);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => TaskComment::class,
        'subject_id' => $comment->id,
        'causer_id' => $user->id,
        'log_name' => 'task_comment',
        'description' => 'updated'
    ]);
});

it('logs task comment soft deletes', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $comment = TaskComment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'comment' => 'Test comment'
    ]);

    $this->actingAs($user, 'sanctum');

    $comment->delete();

    $this->assertSoftDeleted('task_comments', ['id' => $comment->id]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => TaskComment::class,
        'subject_id' => $comment->id,
        'causer_id' => $user->id,
        'log_name' => 'task_comment',
        'description' => 'deleted'
    ]);
});
