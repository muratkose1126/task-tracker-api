<?php

use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ProjectMemberController;
use App\Http\Controllers\Api\V1\TaskAttachmentController;
use App\Http\Controllers\Api\V1\TaskCommentController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->as('v1.')->group(function () {
    Route::apiResource('projects', ProjectController::class)
        ->middleware('auth:sanctum');

    Route::apiResource('projects.tasks', TaskController::class)
        ->middleware('auth:sanctum');

    Route::apiResource('tasks.comments', TaskCommentController::class)
        ->shallow()
        ->middleware('auth:sanctum');

    Route::apiResource('tasks.attachments', TaskAttachmentController::class)
        ->except(['show', 'update'])
        ->shallow()
        ->middleware('auth:sanctum');

    Route::apiResource('projects.members', ProjectMemberController::class)
        ->middleware('auth:sanctum');
});
