<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SessionAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Token-based auth (for mobile/Postman)
    Route::post('register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('login', [AuthController::class, 'login'])->middleware('guest');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Session-based auth (for web browser)
    Route::prefix('session')->group(function () {
        Route::post('register', [SessionAuthController::class, 'register'])->middleware('guest');
        Route::post('login', [SessionAuthController::class, 'login'])->middleware('guest');
        Route::get('me', [SessionAuthController::class, 'me'])->middleware('auth:web');
        Route::post('logout', [SessionAuthController::class, 'logout']);
    });
});
