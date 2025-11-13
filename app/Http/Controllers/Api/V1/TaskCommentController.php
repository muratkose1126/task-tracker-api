<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\TaskComment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\V1\TaskCommentResource;
use App\Http\Requests\V1\StoreTaskCommentRequest;
use App\Http\Requests\V1\UpdateTaskCommentRequest;

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
    public function store(StoreTaskCommentRequest $request, Task $task)
    {
        $validated = $request->validated();

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
    public function update(UpdateTaskCommentRequest $request, TaskComment $comment)
    {
        $validated = $request->validated();

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
