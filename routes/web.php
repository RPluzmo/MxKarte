<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RiderController;

Route::get('/', [MapController::class, 'index']);

Route::get('/tracks/{track}', [MapController::class, 'show'])->name('tracks.show');
Route::post('/tracks/{track}', [RiderController::class, 'store'])->name('riders.store');