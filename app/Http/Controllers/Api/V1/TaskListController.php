<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TaskList;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Space $space)
    {
        Gate::authorize('view', $space);

        $lists = $space->lists()->with('tasks')->get();

        return response()->json(['data' => $lists]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Space $space)
    {
        Gate::authorize('view', $space);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'group_id' => 'nullable|exists:groups,id',
            'status_schema' => 'nullable|array',
        ]);

        $validated['space_id'] = $space->id;

        $list = TaskList::create($validated);

        return response()->json(['data' => $list], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskList $list)
    {
        Gate::authorize('view', $list->space);

        return response()->json(['data' => $list->load(['space', 'group', 'tasks'])]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskList $list)
    {
        Gate::authorize('update', $list->space);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'group_id' => 'nullable|exists:groups,id',
            'status_schema' => 'nullable|array',
            'is_archived' => 'sometimes|boolean',
        ]);

        $list->update($validated);

        return response()->json(['data' => $list]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskList $list)
    {
        Gate::authorize('delete', $list->space);

        $list->delete();

        return response()->json(null, 204);
    }
}
