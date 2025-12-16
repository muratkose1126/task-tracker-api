<?php

use App\Models\Space;
use App\Models\User;
use App\Models\Workspace;

test('workspace belongs to owner', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    expect($workspace->owner->is($owner))->toBeTrue();
});

test('workspace has many spaces', function () {
    $workspace = Workspace::factory()->create();
    Space::factory()->for($workspace)->count(3)->create();
    expect($workspace->spaces)->toHaveCount(3);
});

test('workspace has name', function () {
    $workspace = Workspace::factory()->create(['name' => 'Test Workspace']);
    expect($workspace->name)->toBe('Test Workspace');
});

test('workspace has description', function () {
    $workspace = Workspace::factory()->create(['description' => 'Test Description']);
    expect($workspace->description)->toBe('Test Description');
});

test('workspace has slug', function () {
    $workspace = Workspace::factory()->create(['slug' => 'test-slug']);
    expect($workspace->slug)->toBe('test-slug');
});

test('workspace settings are json', function () {
    $workspace = Workspace::factory()->create(['settings' => ['color' => 'blue']]);
    expect($workspace->settings)->toEqual(['color' => 'blue']);
});

test('workspace timestamps are set', function () {
    $workspace = Workspace::factory()->create();
    expect($workspace->created_at)->not()->toBeNull();
    expect($workspace->updated_at)->not()->toBeNull();
});

test('workspace owner id matches', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    expect($workspace->owner_id)->toBe($owner->id);
});

test('workspace can have multiple spaces', function () {
    $workspace = Workspace::factory()->create();
    Space::factory()->for($workspace)->count(5)->create();
    expect($workspace->spaces)->toHaveCount(5);
});

test('workspace member relationship exists', function () {
    $workspace = Workspace::factory()->create();
    expect($workspace->members)->not()->toBeNull();
});
