<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommitController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('posts', [PostController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('posts');

Route::get('commits', [CommitController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('commits');

require __DIR__.'/auth.php';
