<?php

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Enums\ProjectRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list project members', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    // user'ı owner olarak ekle
    ProjectMember::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'role' => ProjectRole::OWNER,
    ]);

    ProjectMember::factory()->count(3)->create([
        'project_id' => $project->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/projects/{$project->id}/members");

    $response->assertStatus(200)
        ->assertJsonCount(4, 'data'); // owner + 3 member
});

it('can add a member to project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    // user'ı owner olarak ekle
    ProjectMember::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'role' => ProjectRole::OWNER,
    ]);

    $memberUser = User::factory()->create();

    $payload = [
        'user_id' => $memberUser->id,
        'role' => ProjectRole::DEVELOPER,
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/projects/{$project->id}/members", $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.user_id', $memberUser->id)
        ->assertJsonPath('data.role', ProjectRole::DEVELOPER);

    $this->assertDatabaseHas('project_members', [
        'user_id' => $memberUser->id,
        'project_id' => $project->id,
        'role' => ProjectRole::DEVELOPER,
    ]);
});

it('can update a project member role', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    // user'ı owner olarak ekle
    ProjectMember::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'role' => ProjectRole::OWNER,
    ]);

    $member = ProjectMember::factory()->create([
        'project_id' => $project->id,
        'role' => ProjectRole::DEVELOPER,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/projects/{$project->id}/members/{$member->id}", [
            'role' => ProjectRole::MANAGER,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.role', ProjectRole::MANAGER);

    $this->assertDatabaseHas('project_members', [
        'id' => $member->id,
        'role' => ProjectRole::MANAGER,
    ]);
});

it('can show a project member', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    // user'ı owner olarak ekle
    ProjectMember::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'role' => ProjectRole::OWNER,
    ]);

    $member = ProjectMember::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/projects/{$project->id}/members/{$member->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $member->id);
});

it('can delete a project member', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    // user'ı owner olarak ekle
    ProjectMember::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'role' => ProjectRole::OWNER,
    ]);

    $member = ProjectMember::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/projects/{$project->id}/members/{$member->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('project_members', [
        'id' => $member->id,
    ]);
});

it('fails when role is invalid', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    // user'ı owner olarak ekle
    ProjectMember::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'role' => ProjectRole::OWNER,
    ]);

    $memberUser = User::factory()->create();

    $payload = [
        'user_id' => $memberUser->id,
        'role' => 'invalid_role',
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/projects/{$project->id}/members", $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['role']);
});
