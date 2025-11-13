<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use App\Enums\ProjectRole;
use Gate;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProjectResource;
use App\Http\Requests\V1\StoreProjectRequest;
use App\Http\Requests\V1\UpdateProjectRequest;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize("viewAny", Project::class);

        return ProjectResource::collection(Project::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        $project = Project::create($validated);

        $project->members()->create([
            'user_id' => auth()->id(),
            'role' => ProjectRole::OWNER,
        ]);

        return new ProjectResource($project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        Gate::authorize("view", $project);

        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

        $project->update($validated);

        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        Gate::authorize("delete", $project);

        $project->delete();
        return response()->noContent();
    }
}

