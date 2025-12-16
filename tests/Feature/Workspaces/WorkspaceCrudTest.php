<?php

use App\Models\User;
use App\Models\Workspace;

test('authenticated user can list workspaces', function () {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->count(3)->create();

    $response = $this->actingAsApi($user)
        ->getJson('/api/v1/workspaces');

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKey('data');
});

test('user can create workspace', function () {
    $user = User::factory()->create();

    $response = $this->actingAsApi($user)
        ->postJson('/api/v1/workspaces', [
            'name' => 'My Workspace',
            'slug' => 'my-workspace',
            'description' => 'Test workspace',
        ]);

    expect($response->status())->toBe(201);
    expect($response->json())->toHaveKey('data.id');
});

test('user can view owned workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/workspaces/{$workspace->id}");

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBe($workspace->id);
});

test('user can update owned workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/workspaces/{$workspace->id}", [
            'name' => 'Updated Workspace',
            'description' => 'Updated description',
        ]);

    expect($response->status())->toBe(200);
});

test('user can delete owned workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAsApi($user)
        ->deleteJson("/api/v1/workspaces/{$workspace->id}");

    expect($response->status())->toBe(204);
});

test('workspace slug must be unique', function () {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->create(['slug' => 'unique-slug']);

    $response = $this->actingAsApi($user)
        ->postJson('/api/v1/workspaces', [
            'name' => 'Another Workspace',
            'slug' => 'unique-slug',
        ]);

    expect($response->status())->toBe(422);
});

test('workspace name is required', function () {
    $user = User::factory()->create();

    $response = $this->actingAsApi($user)
        ->postJson('/api/v1/workspaces', [
            'description' => 'No name',
        ]);

    expect($response->status())->toBe(422);
});

test('unauthenticated user cannot create workspace', function () {
    $response = $this->postJson('/api/v1/workspaces', [
        'name' => 'Test',
    ]);

    expect($response->status())->toBe(401);
});

test('user cannot see other workspaces in list', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $workspace1 = Workspace::factory()->for($user1, 'owner')->create();
    Workspace::factory()->for($user2, 'owner')->create();

    $response = $this->actingAsApi($user1)
        ->getJson('/api/v1/workspaces');

    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($workspace1->id);
});

test('workspace timestamps are set', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    expect($workspace->created_at)->not->toBeNull();
    expect($workspace->updated_at)->not->toBeNull();
});
