<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list all tasks for a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    Task::factory()->count(3)->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/projects/{$project->id}/tasks");

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('can create a new task for a project', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson("/api/v1/projects/{$project->id}/tasks", [
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
        'project_id' => $project->id,
    ]);
});

it('can show a task from a project', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->actingAs($task->user, 'sanctum')->getJson("/api/v1/projects/{$project->id}/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
            ],
        ]);
});

it('returns 404 when task does not belong to project', function () {
    $project = Project::factory()->create();
    $otherProject = Project::factory()->create();
    $task = Task::factory()->create([
        'project_id' => $otherProject->id,
    ]);

    $response = $this->actingAs($task->user, 'sanctum')->getJson("/api/v1/projects/{$project->id}/tasks/{$task->id}");

    $response->assertStatus(404);
});

it('can update a task in a project', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->actingAs($task->user, 'sanctum')->putJson("/api/v1/projects/{$project->id}/tasks/{$task->id}", [
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

it('can delete a task from a project', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->actingAs($task->user, 'sanctum')->deleteJson("/api/v1/projects/{$project->id}/tasks/{$task->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('tasks', [
        'id' => $task->id,
    ]);
});
