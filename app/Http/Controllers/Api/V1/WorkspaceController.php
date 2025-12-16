<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $workspaces = Workspace::whereHas('members', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->orWhere('owner_id', $request->user()->id)
          ->with(['owner:id,name,email', 'spaces'])
          ->get();

        // Attach last_visited_path for each workspace
        $workspaces->each(function ($workspace) use ($request) {
            $member = $workspace->members()
                ->where('user_id', $request->user()->id)
                ->first();
            $workspace->last_visited_path = $member?->last_visited_path;
        });

        return response()->json(['data' => $workspaces]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $workspace = Workspace::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
            'owner_id' => $request->user()->id,
            'description' => $validated['description'] ?? null,
        ]);

        // Auto-add owner as workspace member
        $workspace->members()->create([
            'user_id' => $request->user()->id,
            'role' => 'owner',
        ]);

        return response()->json(['data' => $workspace->load('owner')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Workspace $workspace)
    {
        Gate::authorize('view', $workspace);

        return response()->json(['data' => $workspace->load(['owner', 'spaces', 'members.user'])]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workspace $workspace)
    {
        Gate::authorize('update', $workspace);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
        ]);

        $workspace->update($validated);

        return response()->json(['data' => $workspace]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workspace $workspace)
    {
        Gate::authorize('delete', $workspace);

        $workspace->delete();

        return response()->json(null, 204);
    }

    /**
     * Update last visited path for the authenticated user in this workspace.
     */
    public function updateLastVisited(Request $request, Workspace $workspace)
    {
        Gate::authorize('view', $workspace);

        $validated = $request->validate([
            'path' => 'required|string|max:500',
        ]);

        $workspace->members()
            ->where('user_id', $request->user()->id)
            ->update(['last_visited_path' => $validated['path']]);

        return response()->json(['success' => true]);
    }
}
