<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Services\V1\TaskCommentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates, updates and deletes a task comment', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $service = new TaskCommentService;

    $comment = $service->create($task, [
        'user_id' => $user->id,
        'comment' => 'Hello',
        'type' => 'note',
    ]);

    expect(TaskComment::count())->toBe(1);
    expect($comment->comment)->toBe('Hello');

    $comment = $service->update($comment, ['comment' => 'Updated']);
    expect($comment->comment)->toBe('Updated');

    $service->delete($comment);
    expect(TaskComment::count())->toBe(0);
});
