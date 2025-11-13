<?php

namespace App\Services\V1;

use App\Models\Project;
use App\Models\ProjectMember;

class ProjectMemberService
{
    public function add(Project $project, array $data): ProjectMember
    {
        return $project->members()->create($data);
    }

    public function update(ProjectMember $member, array $data): ProjectMember
    {
        $member->update($data);
        return $member;
    }

    public function remove(ProjectMember $member): void
    {
        $member->delete();
    }
}
