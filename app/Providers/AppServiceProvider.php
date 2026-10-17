<?php

namespace App\Providers;

use App\Models\Track;
use App\Models\TrackAnnouncement;
use App\Models\TrackComment;
use App\Policies\TrackAnnouncementPolicy;
use App\Policies\TrackCommentPolicy;
use App\Policies\TrackPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('pagination.default');
        Gate::policy(Track::class, TrackPolicy::class);
        Gate::policy(TrackAnnouncement::class, TrackAnnouncementPolicy::class);
        Gate::policy(TrackComment::class, TrackCommentPolicy::class);
    }
}
