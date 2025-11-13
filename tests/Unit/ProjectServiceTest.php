<?php

use App\Models\Project;
use App\Models\User;
use App\Enums\ProjectRole;
use App\Services\V1\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates project and assigns owner', function () {
    $owner = User::factory()->create();

    $service = new ProjectService();

    $project = $service->create([
        'name' => 'New Project',
        'description' => 'Description',
    ], $owner);

    expect(Project::count())->toBe(1);
    expect($project->name)->toBe('New Project');

    $this->assertDatabaseHas('project_members', [
        'project_id' => $project->id,
        'user_id' => $owner->id,
        'role' => ProjectRole::OWNER,
    ]);
});
