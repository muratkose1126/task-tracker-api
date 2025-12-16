<?php

use App\Models\Group;
use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\TaskList;
use App\Models\Workspace;

test('space belongs to workspace', function () {
    $workspace = Workspace::factory()->create();
    $space = Space::factory()->for($workspace)->create();
    expect($space->workspace->is($workspace))->toBeTrue();
});

test('space has many members', function () {
    $space = Space::factory()->create();
    SpaceMember::factory()->for($space)->count(3)->create();
    expect($space->members)->toHaveCount(3);
});

test('space has many groups', function () {
    $space = Space::factory()->create();
    Group::factory()->for($space)->count(4)->create();
    expect($space->groups)->toHaveCount(4);
});

test('space has many lists', function () {
    $space = Space::factory()->create();
    TaskList::factory()->for($space)->count(5)->create();
    expect($space->lists)->toHaveCount(5);
});

test('space visibility can be public', function () {
    $space = Space::factory()->public()->create();
    expect($space->visibility)->toBe('public');
});

test('space visibility can be private', function () {
    $space = Space::factory()->private()->create();
    expect($space->visibility)->toBe('private');
});

test('space can be archived', function () {
    $space = Space::factory()->archived()->create();
    expect($space->is_archived)->toBeTrue();
});

test('space can be active', function () {
    $space = Space::factory()->create(['is_archived' => false]);
    expect($space->is_archived)->toBeFalse();
});

test('space has color attribute', function () {
    $space = Space::factory()->create(['color' => '#FF5733']);
    expect($space->color)->toBe('#FF5733');
});

test('space timestamps are set', function () {
    $space = Space::factory()->create();
    expect($space->created_at)->not()->toBeNull();
    expect($space->updated_at)->not()->toBeNull();
});
