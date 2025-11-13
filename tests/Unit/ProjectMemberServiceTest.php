<?php

use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Services\V1\ProjectMemberService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('adds, updates and removes project member', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->create();

    $service = new ProjectMemberService;

    $member = $service->add($project, [
        'user_id' => $owner->id,
        'role' => ProjectRole::OWNER,
    ]);

    expect(ProjectMember::count())->toBe(1);
    expect($member->role)->toBe(ProjectRole::OWNER);

    $member = $service->update($member, ['role' => ProjectRole::OWNER]);
    expect($member->role)->toBe(ProjectRole::OWNER);

    $service->remove($member);
    expect(ProjectMember::count())->toBe(0);
});
