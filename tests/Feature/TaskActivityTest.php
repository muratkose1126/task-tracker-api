<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs task creation', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum');

    $task = Task::factory()->create([
        'user_id' => $user->id,
        'title' => 'Test Task',
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Task::class,
        'subject_id' => $task->id,
        'causer_id' => $user->id,
        'log_name' => 'task',
        'description' => 'created',
    ]);
});

it('logs task updates', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum');

    $task->update(['title' => 'Updated Task']);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Task::class,
        'subject_id' => $task->id,
        'causer_id' => $user->id,
        'log_name' => 'task',
        'description' => 'updated',
    ]);
});

it('logs task soft deletes', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum');

    $task->delete();

    $this->assertSoftDeleted('tasks', ['id' => $task->id]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Task::class,
        'subject_id' => $task->id,
        'causer_id' => $user->id,
        'log_name' => 'task',
        'description' => 'deleted',
    ]);
});
