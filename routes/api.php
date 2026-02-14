<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClipController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SavedVideoController;
use App\Http\Controllers\Api\TranscriptController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\VideoStreamController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:300,1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);

    Route::get('videos/stream/{video}', [VideoStreamController::class, 'stream']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('videos', [VideoController::class, 'index']);
        Route::get('videos/filters', [VideoController::class, 'filters']);
        Route::get('videos/{video}', [VideoController::class, 'show']);
        Route::get('videos/{video}/transcript', [TranscriptController::class, 'show']);

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [ProfileController::class, 'me']);
        Route::get('me/stats', [ProfileController::class, 'stats']);
        Route::get('me/progress', [ProfileController::class, 'progress']);
        Route::get('me/progress/{video}', [ProfileController::class, 'progressForVideo']);
        Route::post('me/progress', [ProfileController::class, 'storeProgress']);

        Route::get('me/saved-videos', [SavedVideoController::class, 'index']);
        Route::post('me/saved-videos', [SavedVideoController::class, 'store']);
        Route::delete('me/saved-videos/{videoId}', [SavedVideoController::class, 'destroy']);

        Route::post('videos', [VideoController::class, 'store']);
        Route::put('videos/{video}', [VideoController::class, 'update']);
        Route::delete('videos/{video}', [VideoController::class, 'destroy']);

        Route::post('videos/{video}/transcript', [TranscriptController::class, 'store']);

        Route::get('clips', [ClipController::class, 'index']);
        Route::post('clips', [ClipController::class, 'store']);
        Route::delete('clips/{clip}', [ClipController::class, 'destroy']);
    });
});
