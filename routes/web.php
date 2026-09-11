<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ProfileController;

Route::get('/', [MapController::class, 'index'])->name('home');

Route::get('/tracks/{track}', [MapController::class, 'show'])->name('tracks.show');
Route::post('/tracks/{track}', [RiderController::class, 'store'])->name('riders.store');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register');
Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store'])->name('login');
Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');