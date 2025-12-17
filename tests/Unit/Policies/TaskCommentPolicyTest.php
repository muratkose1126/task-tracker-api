<?php

use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;

test('task owner can view task comment', function () {
    $taskOwner = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($taskOwner)->allows('view', $comment))->toBeTrue();
});

test('comment author can view own comment', function () {
    $taskOwner = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($commenter)->allows('view', $comment))->toBeTrue();
});

test('other user cannot view task comment', function () {
    $taskOwner = User::factory()->create();
    $commenter = User::factory()->create();
    $otherUser = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($otherUser)->allows('view', $comment))->toBeFalse();
});

test('comment author can update own comment', function () {
    $taskOwner = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($commenter)->allows('update', $comment))->toBeTrue();
});

test('task owner can update any comment on their task', function () {
    $taskOwner = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($taskOwner)->allows('update', $comment))->toBeTrue();
});

test('space admin can update comment', function () {
    $taskOwner = User::factory()->create();
    $spaceAdmin = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceAdmin)
        ->state(['role' => 'member'])
        ->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($commenter)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceAdmin)
        ->admin()
        ->create();

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceAdmin)->allows('update', $comment))->toBeTrue();
});

test('space editor can update comment', function () {
    $taskOwner = User::factory()->create();
    $spaceEditor = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceEditor)
        ->state(['role' => 'member'])
        ->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($commenter)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceEditor)
        ->state(['role' => 'editor'])
        ->create();

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceEditor)->allows('update', $comment))->toBeTrue();
});

test('space member cannot update comment', function () {
    $taskOwner = User::factory()->create();
    $spaceMember = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($commenter)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceMember)
        ->state(['role' => 'member'])
        ->create();

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceMember)->allows('update', $comment))->toBeFalse();
});

test('comment author can delete own comment', function () {
    $taskOwner = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($commenter)->allows('delete', $comment))->toBeTrue();
});

test('task owner can delete any comment on their task', function () {
    $taskOwner = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();
    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($taskOwner)->allows('delete', $comment))->toBeTrue();
});

test('space admin can delete comment', function () {
    $taskOwner = User::factory()->create();
    $spaceAdmin = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceAdmin)
        ->state(['role' => 'member'])
        ->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($commenter)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceAdmin)
        ->admin()
        ->create();

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceAdmin)->allows('delete', $comment))->toBeTrue();
});

test('space editor cannot delete comment', function () {
    $taskOwner = User::factory()->create();
    $spaceEditor = User::factory()->create();
    $commenter = User::factory()->create();

    $workspace = Workspace::factory()->for($taskOwner, 'owner')->create();
    $space = Space::factory()->for($workspace)->state(['visibility' => 'public'])->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($spaceEditor)
        ->state(['role' => 'member'])
        ->create();

    WorkspaceMember::factory()
        ->for($workspace)
        ->for($commenter)
        ->state(['role' => 'member'])
        ->create();

    SpaceMember::factory()
        ->for($space)
        ->for($spaceEditor)
        ->state(['role' => 'editor'])
        ->create();

    $list = TaskList::factory()->for($space)->create();
    $task = Task::factory()->for($list)->for($taskOwner, 'user')->create();
    $comment = TaskComment::factory()->for($task)->for($commenter)->create();

    expect(\Illuminate\Support\Facades\Gate::forUser($spaceEditor)->allows('delete', $comment))->toBeFalse();
});
