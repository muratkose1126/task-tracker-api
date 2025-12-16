<?php

use App\Http\Controllers\Api\V1\TaskAttachmentController;
use App\Http\Controllers\Api\V1\TaskCommentController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\WorkspaceController;
use App\Http\Controllers\Api\V1\SpaceController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\TaskListController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->as('v1.')->group(function () {
    // New hierarchy: Workspaces → Spaces → Groups → Lists → Tasks
    Route::apiResource('workspaces', WorkspaceController::class)
        ->middleware('auth:sanctum');

    Route::post('workspaces/{workspace}/last-visited', [WorkspaceController::class, 'updateLastVisited'])
        ->middleware('auth:sanctum')
        ->name('workspaces.update-last-visited');

    Route::apiResource('workspaces.spaces', SpaceController::class)
        ->shallow()
        ->middleware('auth:sanctum');

    Route::apiResource('spaces.groups', GroupController::class)
        ->shallow()
        ->middleware('auth:sanctum');

    // Tasks for group and space (aggregate across lists)
    Route::get('groups/{group}/tasks', [\App\Http\Controllers\Api\V1\GroupController::class, 'tasks'])
        ->middleware('auth:sanctum')
        ->name('groups.tasks.index');

    Route::get('spaces/{space}/tasks', [\App\Http\Controllers\Api\V1\SpaceController::class, 'tasks'])
        ->middleware('auth:sanctum')
        ->name('spaces.tasks.index');

    Route::apiResource('spaces.lists', TaskListController::class)
        ->shallow()
        ->middleware('auth:sanctum');

    // (top-level tasks index registered explicitly below)

    Route::apiResource('lists.tasks', TaskController::class)
        ->shallow()
        ->middleware('auth:sanctum');

    // Top-level tasks index (all tasks)
    Route::get('tasks', [TaskController::class, 'indexAll'])
        ->middleware('auth:sanctum')
        ->name('tasks.index');

    Route::apiResource('tasks.comments', TaskCommentController::class)
        ->shallow()
        ->middleware('auth:sanctum');

    Route::apiResource('tasks.attachments', TaskAttachmentController::class)
        ->except(['show', 'update'])
        ->shallow()
        ->middleware('auth:sanctum');
});
