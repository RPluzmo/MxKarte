<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SiteAnnouncementController as AdminSiteAnnouncementController;
use App\Http\Controllers\Admin\TrackAnnouncementController as AdminTrackAnnouncementController;
use App\Http\Controllers\Admin\TrackController as AdminTrackController;
use App\Http\Controllers\Admin\TrackOwnerAccessRequestController as AdminTrackOwnerAccessRequestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TrackAnnouncementController;
use App\Http\Controllers\TrackCommentController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\TrackPreferenceController;
use App\Http\Controllers\TrackOwnerAccessRequestController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [MapController::class, 'index'])->name('home');

Route::get('/tracks/{track}', [MapController::class, 'show'])->name('tracks.show');
Route::post('/tracks/{track}', [RiderController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('riders.store');
Route::delete('/riders/{rider}', [RiderController::class, 'destroy'])->name('riders.destroy');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('register.store');
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
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
    Route::put('/track-preferences', [TrackPreferenceController::class, 'update'])->name('track-preferences.update');
    Route::get('/owner-access-requests', [TrackOwnerAccessRequestController::class, 'index'])->name('owner-requests.index');
    Route::get('/owner-access-requests/create', [TrackOwnerAccessRequestController::class, 'create'])->name('owner-requests.create');
    Route::post('/owner-access-requests', [TrackOwnerAccessRequestController::class, 'store'])
        ->middleware('throttle:5,10')
        ->name('owner-requests.store');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('users', AdminUserController::class)->except('show');
    Route::get('owner-access-requests', [AdminTrackOwnerAccessRequestController::class, 'index'])->name('owner-requests.index');
    Route::put('owner-access-requests/{ownerAccessRequest}/approve', [AdminTrackOwnerAccessRequestController::class, 'approve'])->name('owner-requests.approve');
    Route::put('owner-access-requests/{ownerAccessRequest}/reject', [AdminTrackOwnerAccessRequestController::class, 'reject'])->name('owner-requests.reject');
    Route::resource('tracks', AdminTrackController::class)->except('show');
    Route::resource('announcements', AdminSiteAnnouncementController::class)->except('show');
    Route::resource('track-announcements', AdminTrackAnnouncementController::class)->except('show');
});
