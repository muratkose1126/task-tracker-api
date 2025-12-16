<?php

use App\Models\Group;
use App\Models\Space;
use App\Models\TaskList;

test('group belongs to space', function () {
    $space = Space::factory()->create();
    $group = Group::factory()->for($space)->create();
    expect($group->space->is($space))->toBeTrue();
});

test('group has many lists', function () {
    $group = Group::factory()->create();
    TaskList::factory()->for($group)->count(3)->create();
    expect($group->lists)->toHaveCount(3);
});

test('group has name', function () {
    $group = Group::factory()->create(['name' => 'Test Group']);
    expect($group->name)->toBe('Test Group');
});

test('group has color', function () {
    $group = Group::factory()->create(['color' => '#FF5733']);
    expect($group->color)->toBe('#FF5733');
});

test('group deletion does not cascade to lists', function () {
    $group = Group::factory()->create();
    $lists = TaskList::factory()->for($group)->count(2)->create();
    $group->delete();
    expect(TaskList::query()->count())->toBe(2);
});
