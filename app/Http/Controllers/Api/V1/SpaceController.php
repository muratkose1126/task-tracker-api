<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SpaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Workspace $workspace)
    {
        Gate::authorize('view', $workspace);

        $query = $workspace->spaces();

        // Filter by visibility (public spaces or private spaces where user is member)
        if (! $workspace->members()->where('user_id', $request->user()->id)->exists()) {
            $query->where('visibility', 'public');
        } else {
            $query->where(function ($q) use ($request) {
                $q->where('visibility', 'public')
                    ->orWhereHas('members', fn ($q2) => $q2->where('user_id', $request->user()->id));
            });
        }

        $spaces = $query->with(['groups', 'lists'])->get();

        return response()->json(['data' => $spaces]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Workspace $workspace)
    {
        Gate::authorize('view', $workspace);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'visibility' => 'required|in:public,private',
            'color' => 'nullable|string|max:7',
        ]);

        $space = $workspace->spaces()->create($validated);

        // If private, auto-add creator as admin
        if ($validated['visibility'] === 'private') {
            $space->members()->create([
                'user_id' => $request->user()->id,
                'role' => 'admin',
            ]);
        }

        return response()->json(['data' => $space], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Space $space)
    {
        Gate::authorize('view', $space);

        return response()->json(['data' => $space->load(['workspace', 'groups', 'lists.tasks', 'members'])]);
    }

    /**
     * Display tasks for the given space (all tasks in lists under space)
     */
    public function tasks(Space $space)
    {
        Gate::authorize('view', $space);

        $tasks = Task::whereHas('list', fn ($q) => $q->where('space_id', $space->id))->get();

        return \App\Http\Resources\V1\TaskResource::collection($tasks);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Space $space)
    {
        Gate::authorize('update', $space);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'visibility' => 'sometimes|in:public,private',
            'color' => 'nullable|string|max:7',
            'is_archived' => 'sometimes|boolean',
        ]);

        $space->update($validated);

        return response()->json(['data' => $space]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Space $space)
    {
        Gate::authorize('delete', $space);

        $space->delete();

        return response()->json(null, 204);
    }
}
