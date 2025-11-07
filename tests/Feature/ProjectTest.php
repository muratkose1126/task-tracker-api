<?php

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list all projects', function () {
    $user = User::factory()->create();
    Project::factory()->count(3)->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/projects');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('can create a new project', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/projects', [
        'name' => 'New Project',
        'description' => 'Project description',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'created_at',
                'updated_at',
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'name' => 'New Project',
    ]);
});

it('can show a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/projects/{$project->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
            ],
        ]);
});

it('can update a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->putJson("/api/v1/projects/{$project->id}", [
        'name' => 'Updated Name',
        'description' => 'Updated description',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $project->id,
                'name' => 'Updated Name',
                'description' => 'Updated description',
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Name',
    ]);
});

it('can delete a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/projects/{$project->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('projects', [
        'id' => $project->id,
    ]);
});
