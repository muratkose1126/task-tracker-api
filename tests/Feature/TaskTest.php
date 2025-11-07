<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\TaskComment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list all tasks', function () {
    $user = User::factory()->create();
    Task::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/tasks');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('can create a new task', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/tasks', [
        'project_id' => $project->id,
        'user_id' => $user->id,
        'title' => 'Test Task',
        'description' => 'This is a test task',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'project_id',
                'user_id',
                'title',
                'description',
                'created_at',
                'updated_at',
            ],
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
    ]);
});

it('can show a task', function () {
    $task = Task::factory()->create();

    $response = $this->actingAs($task->user, 'sanctum')->getJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
            ],
        ]);
});

it('can update a task', function () {
    $task = Task::factory()->create();

    $response = $this->actingAs($task->user, 'sanctum')->putJson("/api/v1/tasks/{$task->id}", [
        'title' => 'Updated Task Title',
        'description' => 'Updated description',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => 'Updated Task Title',
            ],
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Task Title',
    ]);
});

it('can delete a task', function () {
    $task = Task::factory()->create();

    $response = $this->actingAs($task->user, 'sanctum')->deleteJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('tasks', [
        'id' => $task->id,
    ]);
});
