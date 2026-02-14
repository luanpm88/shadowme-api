<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AdminTranscriptController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVideoController;
use App\Http\Controllers\Auth\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])->name('login.store');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin', AdminController::class);
    Route::get('/admin/videos', [AdminVideoController::class, 'index'])->name('admin.videos.index');
    Route::get('/admin/videos/create', [AdminVideoController::class, 'create'])->name('admin.videos.create');
    Route::post('/admin/videos', [AdminVideoController::class, 'store'])->name('admin.videos.store');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');

    Route::get('/admin/videos/{video}/transcript', [AdminTranscriptController::class, 'edit'])
        ->name('admin.videos.transcript');
    Route::post('/admin/videos/{video}/transcript', [AdminTranscriptController::class, 'update'])
        ->name('admin.videos.transcript.update');
    Route::post('/admin/videos/{video}/transcript/auto', [AdminTranscriptController::class, 'auto'])
        ->name('admin.videos.transcript.auto');
});
