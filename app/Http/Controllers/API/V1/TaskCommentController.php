<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TaskCommentResource;

class TaskCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Task $task)
    {
        return TaskCommentResource::collection($task->comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Task $task)
    {
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
    public function show(TaskComment $taskComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskComment $taskComment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskComment $taskComment)
    {
        //
    }
}
