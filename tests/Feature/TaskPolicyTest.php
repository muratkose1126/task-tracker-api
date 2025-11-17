<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows the owner to update their own task', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/projects/{$project->id}/tasks/{$task->id}", [
            'title' => 'Updated title',
        ])
        ->assertStatus(200);
});

it('denies other users from updating the task', function () {
    $project = Project::factory()->create();
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $owner->id,
        'project_id' => $project->id,
    ]);

    $this->actingAs($other, 'sanctum')
        ->putJson("/api/v1/projects/{$project->id}/tasks/{$task->id}", [
            'title' => 'Hacked!',
        ])
        ->assertStatus(403);
});

it('allows the owner to delete their own task', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/projects/{$project->id}/tasks/{$task->id}")
        ->assertStatus(204);
});

it('denies other users from deleting the task', function () {
    $project = Project::factory()->create();
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $owner->id,
        'project_id' => $project->id,
    ]);

    $this->actingAs($other, 'sanctum')
        ->deleteJson("/api/v1/projects/{$project->id}/tasks/{$task->id}")
        ->assertStatus(403);
});
