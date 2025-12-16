<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreTaskRequest;
use App\Http\Requests\V1\UpdateTaskRequest;
use App\Http\Resources\V1\TaskResource;
use App\Models\Task;
use App\Models\TaskList;
use App\Services\V1\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, TaskList $list)
    {
        Gate::authorize('viewAny', Task::class);

        $tasks = $list->tasks()
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->priority, fn ($q) => $q->where('priority', $request->priority))
            ->get();

        return TaskResource::collection($tasks);
    }

    /**
     * Top-level index: return all tasks, supports optional filtering by space_id, group_id, list_id, status, priority
     */
    public function indexAll(Request $request)
    {
        Gate::authorize('viewAny', Task::class);

        $query = Task::query()->with(['list', 'user']);

        if ($request->has('list_id')) {
            $query->where('list_id', $request->list_id);
        }

        if ($request->has('group_id')) {
            $query->whereHas('list', fn ($q) => $q->where('group_id', $request->group_id));
        }

        if ($request->has('space_id')) {
            $query->whereHas('list', fn ($q) => $q->where('space_id', $request->space_id));
        }

        $query->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->priority, fn ($q) => $q->where('priority', $request->priority));

        $tasks = $query->get();

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, TaskList $list)
    {
        Gate::authorize('view', $list->space);

        $validated = $request->validated();
        $validated['list_id'] = $list->id;
        $validated['user_id'] = $request->user()->id;

        $task = $this->taskService->create($validated);

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $validated = $request->validated();

        $task = $this->taskService->update($task, $validated);

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $this->taskService->delete($task);

        return response()->noContent();
    }
}
