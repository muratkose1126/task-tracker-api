<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ProjectMemberController;
use App\Http\Controllers\Api\V1\TaskAttachmentController;
use App\Http\Controllers\Api\V1\TaskCommentController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;

// API Documentation
Route::get('/docs', function () {
    return view('swagger-ui', [
        'spec' => json_decode(file_get_contents(storage_path('api-docs.yaml'))),
    ]);
})->name('docs');

Route::prefix('v1')->as('v1.')->group(function () {

    Route::prefix('auth')->as('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])
            ->middleware('guest')
            ->name('register');

        Route::post('login', [AuthController::class, 'login'])
            ->middleware('guest')
            ->name('login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me'])
                ->name('me');

            Route::post('logout', [AuthController::class, 'logout'])
                ->name('logout');
        });
    });

    Route::apiResource('projects', ProjectController::class)
        ->middleware('auth:sanctum');

    Route::apiResource('tasks', TaskController::class)
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
