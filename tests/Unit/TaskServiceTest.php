<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\V1\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates, updates and deletes a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $service = new TaskService;

    $task = $service->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'title' => 'Test Task',
        'description' => 'Test description',
    ]);

    expect(Task::count())->toBe(1);
    expect($task->title)->toBe('Test Task');

    $task = $service->update($task, ['title' => 'Updated Title']);
    expect($task->title)->toBe('Updated Title');

    $service->delete($task);
    expect(Task::count())->toBe(0);
});
