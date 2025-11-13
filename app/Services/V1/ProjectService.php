<?php

namespace App\Services\V1;

use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\User;

class ProjectService
{
    /**
     * Create a project and attach the owner as a member.
     */
    public function create(array $data, User $owner): Project
    {
        $project = Project::create($data);

        $project->members()->create([
            'user_id' => $owner->id,
            'role' => ProjectRole::OWNER,
        ]);

        return $project;
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }
}
