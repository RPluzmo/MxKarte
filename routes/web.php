<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\TrackCommentController;
use App\Http\Controllers\TrackAnnouncementController;

Route::get('/', [MapController::class, 'index'])->name('home');

Route::get('/tracks/{track}', [MapController::class, 'show'])->name('tracks.show');
    Route::post('/tracks/{track}', [RiderController::class, 'store'])->name('riders.store');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->name('login.store');
});


Route::middleware('auth')->group(function () {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/tracks/{track}/edit', [TrackController::class, 'edit'])->name('tracks.edit');
    Route::put('/tracks/{track}', [TrackController::class, 'update'])->name('tracks.update');
        Route::post('/tracks/{track}/comments', [TrackCommentController::class, 'store'])->name('comments.store');
        Route::delete('/comments/{comment}', [TrackCommentController::class, 'destroy'])->name('comments.destroy');
            Route::post('/tracks/{track}/announcements', [TrackAnnouncementController::class, 'store'])->name('announcements.store');
            Route::put('/announcements/{announcement}', [TrackAnnouncementController::class, 'update'])->name('announcements.update');
            Route::delete('/announcements/{announcement}', [TrackAnnouncementController::class, 'destroy'])->name('announcements.destroy');
});