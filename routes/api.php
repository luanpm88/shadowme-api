<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClipController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TranscriptController;
use App\Http\Controllers\Api\VideoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);

    Route::get('videos', [VideoController::class, 'index']);
    Route::get('videos/{video}', [VideoController::class, 'show']);
    Route::get('videos/{video}/transcript', [TranscriptController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [ProfileController::class, 'me']);
        Route::get('me/stats', [ProfileController::class, 'stats']);
        Route::get('me/progress', [ProfileController::class, 'progress']);

        Route::post('videos', [VideoController::class, 'store']);
        Route::put('videos/{video}', [VideoController::class, 'update']);
        Route::delete('videos/{video}', [VideoController::class, 'destroy']);

        Route::post('videos/{video}/transcript', [TranscriptController::class, 'store']);

        Route::get('clips', [ClipController::class, 'index']);
        Route::post('clips', [ClipController::class, 'store']);
        Route::delete('clips/{clip}', [ClipController::class, 'destroy']);
    });
});
