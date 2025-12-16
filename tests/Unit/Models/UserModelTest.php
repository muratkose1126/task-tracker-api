<?php

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;

test('user has many workspaces', function () {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->count(3)->create();
    expect($user->workspaces)->toHaveCount(3);
});

test('user has many workspace members', function () {
    $user = User::factory()->create();
    $workspace1 = Workspace::factory()->create();
    $workspace2 = Workspace::factory()->create();
    WorkspaceMember::factory()->for($workspace1)->for($user)->create();
    WorkspaceMember::factory()->for($workspace2)->for($user)->create();
    expect($user->workspaceMembers)->toHaveCount(2);
});

test('user has many tasks', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->count(5)->create();
    expect($user->tasks)->toHaveCount(5);
});

test('user has many task comments', function () {
    $user = User::factory()->create();
    TaskComment::factory()->for($user)->count(4)->create();
    expect($user->taskComments)->toHaveCount(4);
});

test('user can be workspace owner', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    expect($workspace->owner->is($user))->toBeTrue();
});

test('user can have email', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    expect($user->email)->toBe('test@example.com');
});

test('user email is unique', function () {
    User::factory()->create(['email' => 'duplicate@example.com']);
    expect(function () {
        User::factory()->create(['email' => 'duplicate@example.com']);
    })->toThrow(\Illuminate\Database\QueryException::class);
});

test('user has api tokens', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token');
    expect($token->plainTextToken)->not()->toBeEmpty();
    expect($user->tokens)->toHaveCount(1);
});
