<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\V1\TaskCommentResource;

class TaskCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Task $task)
    {
        Gate::authorize('viewAny', TaskComment::class);

        $comments = $task->comments()->with('user')->get();
        return TaskCommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Task $task)
    {
        Gate::authorize('create', TaskComment::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'comment' => 'required|string',
            'type' => 'nullable|string',
        ]);

        $comment = $task->comments()->create($validated);

        return new TaskCommentResource($comment);
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskComment $comment)
    {
        Gate::authorize('view', $comment);

        $comment->load('user');
        return new TaskCommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskComment $comment)
    {
        Gate::authorize('update', $comment);

        $validated = $request->validate([
            'comment' => 'sometimes|required|string',
            'type' => 'nullable|string',
        ]);

        $comment->update($validated);

        return new TaskCommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskComment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
