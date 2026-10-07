<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Rider;
use App\Models\SiteAnnouncement;
use App\Models\Track;
use App\Models\TrackAnnouncement;
use App\Models\TrackImage;
use App\Support\RiderChoices;

class MapController extends Controller
{
    public function index()
    {
        $siteAnnouncements = SiteAnnouncement::active()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $tracks = Track::select('id', 'name', 'lat', 'lng', 'description')
            ->with(['images' => fn ($query) => $query->where('type', 'cover')])
            ->withCount('riders')
            ->get();
        $announcementQuery = TrackAnnouncement::with('track')->active();
        $announcementSearch = trim((string) request('announcement_search', ''));
        $announcementTrackId = request()->integer('track_id') ?: null;
        $prioritizePinned = request()->boolean('prioritize_pinned');

        if ($announcementSearch !== '') {
            $announcementQuery->where(function ($query) use ($announcementSearch) {
                $query->where('title', 'like', "%{$announcementSearch}%")
                    ->orWhere('body', 'like', "%{$announcementSearch}%");
            });
        }

        if ($announcementTrackId) {
            $announcementQuery->where('track_id', $announcementTrackId);
        }

        $preferredTrackIds = auth()->user()?->preferredTracks()
            ->pluck('tracks.id')
            ->map(fn ($trackId) => (int) $trackId)
            ->all() ?? [];

        if ($preferredTrackIds) {
            $placeholders = implode(',', array_fill(0, count($preferredTrackIds), '?'));
            $announcementQuery->orderByRaw(
                "CASE WHEN track_id IN ({$placeholders}) THEN 0 ELSE 1 END",
                $preferredTrackIds
            );
        }

        $announcements = $announcementQuery
            ->when($prioritizePinned, fn ($query) => $query->orderByDesc('is_pinned'))
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $clubTrackIds = [];
        $clubName = auth()->user()?->club;

        if ($clubName) {
            $clubTrackIds = Rider::where('club', $clubName)
                ->pluck('track_id')
                ->map(fn ($trackId) => (int) $trackId)
                ->unique()
                ->values()
                ->all();
        }

        return view('map', compact(
            'tracks',
            'siteAnnouncements',
            'clubTrackIds',
            'clubName',
            'announcements',
            'announcementSearch',
            'announcementTrackId',
            'prioritizePinned',
            'preferredTrackIds'
        ));
    }

     public function show(Track $track)
    {
        $track->load([
            'riders' => fn ($query) => $query->upcoming()->orderBy('ride_date')->orderBy('ride_time'),
            'riders.clubModel',
            'comments.user',
            'images',
        ]);
        $track->load(['announcements' => function ($query) {
            $query->active()
                ->orderByDesc('is_pinned')
                ->orderByDesc('published_at');
        }]);
        return view('tracks.show', [
            'track' => $track,
            'clubs' => RiderChoices::clubs(),
            'categories' => RiderChoices::categories(),
            'experienceLevels' => RiderChoices::experienceLevels(),
            'rideDates' => Rider::bookableDates(),
        ]);
    }

}
