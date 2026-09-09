<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;

Route::get('/', [MapController::class, 'index']);

Route::get('/tracks/{track}', [MapController::class, 'show'])->name('tracks.show');
