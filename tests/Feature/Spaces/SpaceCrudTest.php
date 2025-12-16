<?php

use App\Models\Space;
use App\Models\User;
use App\Models\Workspace;

test('workspace member can list spaces', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    Space::factory()->for($workspace)->count(3)->create();

    $response = $this->actingAsApi($owner)
        ->getJson("/api/v1/workspaces/{$workspace->id}/spaces");

    expect($response->status())->toBe(200);
    expect($response->json())->toHaveKey('data');
});

test('user can create space in workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/workspaces/{$workspace->id}/spaces", [
            'name' => 'Development',
            'visibility' => 'public',
            'color' => '#FF5733',
        ]);

    expect($response->status())->toBe(201);
    expect($response->json())->toHaveKey('data.id');
});

test('user can view space', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $space = Space::factory()->for($workspace)->create();

    $response = $this->actingAsApi($user)
        ->getJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(200);
    expect($response->json('data.id'))->toBe($space->id);
});

test('user can update space', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $space = Space::factory()->for($workspace)->create();

    $response = $this->actingAsApi($user)
        ->putJson("/api/v1/spaces/{$space->id}", [
            'name' => 'Updated Space',
            'visibility' => 'private',
        ]);

    expect($response->status())->toBe(200);
});

test('user can delete space', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $space = Space::factory()->for($workspace)->create();

    $response = $this->actingAsApi($user)
        ->deleteJson("/api/v1/spaces/{$space->id}");

    expect($response->status())->toBe(204);
});

test('space visibility is required', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/workspaces/{$workspace->id}/spaces", [
            'name' => 'Test Space',
        ]);

    expect($response->status())->toBe(422);
});

test('space name is required', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAsApi($user)
        ->postJson("/api/v1/workspaces/{$workspace->id}/spaces", [
            'visibility' => 'public',
        ]);

    expect($response->status())->toBe(422);
});

test('space can be public', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $space = Space::factory()->for($workspace)->public()->create();

    expect($space->visibility)->toBe('public');
});

test('space can be private', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $space = Space::factory()->for($workspace)->private()->create();

    expect($space->visibility)->toBe('private');
});

test('unauthenticated user cannot create space', function () {
    $workspace = Workspace::factory()->create();

    $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/spaces", [
        'name' => 'Test',
        'visibility' => 'public',
    ]);

    expect($response->status())->toBe(401);
});
