<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Space;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Space $space)
    {
        Gate::authorize('view', $space);

        $groups = $space->groups()->with('lists.tasks')->get();

        return response()->json(['data' => $groups]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Space $space)
    {
        Gate::authorize('view', $space);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $group = $space->groups()->create($validated);

        return response()->json(['data' => $group], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        Gate::authorize('view', $group->space);

        return response()->json(['data' => $group->load(['space', 'lists.tasks'])]);
    }

    /**
     * Display tasks for the given group (all tasks in lists under group)
     */
    public function tasks(Group $group)
    {
        Gate::authorize('view', $group->space);

        $tasks = Task::whereHas('list', fn ($q) => $q->where('group_id', $group->id))
            ->get();

        return \App\Http\Resources\V1\TaskResource::collection($tasks);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        Gate::authorize('update', $group->space);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $group->update($validated);

        return response()->json(['data' => $group]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        Gate::authorize('delete', $group->space);

        $group->delete();

        return response()->json(null, 204);
    }
}
