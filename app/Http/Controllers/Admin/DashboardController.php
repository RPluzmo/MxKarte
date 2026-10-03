<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\TrackAnnouncement;
use App\Models\SiteAnnouncement;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'usersCount' => User::count(),
            'tracksCount' => Track::count(),
            'announcementsCount' => TrackAnnouncement::count(),
            'siteAnnouncementsCount' => SiteAnnouncement::count(),
        ]);
    }
}
