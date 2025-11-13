<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use App\Enums\ProjectRole;
use Illuminate\Http\Request;
use App\Models\ProjectMember;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\V1\ProjectMemberResource;
use App\Http\Requests\V1\StoreProjectMemberRequest;
use App\Http\Requests\V1\UpdateProjectMemberRequest;

class ProjectMemberController extends Controller
{
    /**
     * Display a listing of the project members.
     */
    public function index(Project $project)
    {
        Gate::authorize('view', $project);

        return ProjectMemberResource::collection(
            $project->members()->with('user')->get()
        );
    }

    /**
     * Store a newly created member in the project.
     */
    public function store(StoreProjectMemberRequest $request, Project $project)
    {
        $validated = $request->validated();

        $member = $project->members()->create($validated);

        return new ProjectMemberResource($member->load('user'));
    }

    /**
     * Display the specified member.
     */
    public function show(Project $project, ProjectMember $member)
    {
        abort_unless($member->project_id === $project->id, 404);
        Gate::authorize('view', $project);

        return new ProjectMemberResource($member->load('user'));
    }

    /**
     * Update the specified member in the project.
     */
    public function update(UpdateProjectMemberRequest $request, Project $project, ProjectMember $member)
    {
        abort_unless($member->project_id === $project->id, 404);

        $validated = $request->validated();

        $member->update($validated);

        return new ProjectMemberResource($member->refresh()->load('user'));
    }

    /**
     * Remove the specified member from the project.
     */
    public function destroy(Project $project, ProjectMember $member)
    {
        abort_unless($member->project_id === $project->id, 404);
        Gate::authorize('delete', $project);

        $member->delete();

        return response()->noContent();
    }
}
