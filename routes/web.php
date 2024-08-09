<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('posts', [PostController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('posts');

require __DIR__.'/auth.php';
