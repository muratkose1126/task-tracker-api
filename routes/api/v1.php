<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskCommentController;

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
        ->except(['show', 'update'])
        ->shallow()
        ->middleware('auth:sanctum');
});
