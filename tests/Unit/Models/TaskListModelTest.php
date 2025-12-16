<?php

use App\Models\Group;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;

test('task list belongs to space', function () {
    $space = Space::factory()->create();
    $list = TaskList::factory()->for($space)->create();
    expect($list->space->is($space))->toBeTrue();
});

test('task list belongs to group nullable', function () {
    $space = Space::factory()->create();
    $list = TaskList::factory()->for($space)->create(['group_id' => null]);
    expect($list->group)->toBeNull();
});

test('task list belongs to group', function () {
    $group = Group::factory()->create();
    $list = TaskList::factory()->for($group)->create();
    expect($list->group->is($group))->toBeTrue();
});

test('task list has many tasks', function () {
    $list = TaskList::factory()->create();
    Task::factory()->forList($list)->count(5)->create();
    expect($list->tasks)->toHaveCount(5);
});

test('task list has name', function () {
    $list = TaskList::factory()->create(['name' => 'Backlog']);
    expect($list->name)->toBe('Backlog');
});

test('task list has status schema as array', function () {
    $schema = ['todo', 'in_progress', 'done'];
    $list = TaskList::factory()->create(['status_schema' => $schema]);
    expect($list->status_schema)->toEqual($schema);
});

test('task list can have null status schema', function () {
    $list = TaskList::factory()->create(['status_schema' => null]);
    expect($list->status_schema)->toBeNull();
});

test('task list can be archived', function () {
    $list = TaskList::factory()->archived()->create();
    expect($list->is_archived)->toBeTrue();
});

test('task list can be active', function () {
    $list = TaskList::factory()->create(['is_archived' => false]);
    expect($list->is_archived)->toBeFalse();
});

test('task list timestamps are set', function () {
    $list = TaskList::factory()->create();
    expect($list->created_at)->not()->toBeNull();
    expect($list->updated_at)->not()->toBeNull();
});
