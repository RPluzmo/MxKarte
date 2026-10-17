<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use App\Models\SiteAnnouncement;
use App\Models\Track;
use App\Models\TrackAnnouncement;
use App\Support\RiderChoices;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'announcement_search' => ['nullable', 'string', 'max:255'],
            'track_id' => ['nullable', 'integer', 'exists:tracks,id'],
            'prioritize_pinned' => ['sometimes', 'boolean'],
        ]);

        $siteAnnouncements = SiteAnnouncement::active()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $tracks = Track::select('id', 'name', 'lat', 'lng', 'description')
            ->with(['images' => fn ($query) => $query->where('type', 'cover')])
            ->withCount('riders')
            ->get();
        $announcementQuery = TrackAnnouncement::with('track')->active();
        $announcementSearch = trim($filters['announcement_search'] ?? '');
        $announcementTrackId = isset($filters['track_id']) ? (int) $filters['track_id'] : null;
        $prioritizePinned = $request->boolean('prioritize_pinned');

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
        $userClub = auth()->user()?->club;
        $clubName = $userClub?->name;

        if ($userClub) {
            $clubTrackIds = Rider::where('club_id', $userClub->id)
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
            'images',
        ]);
        $track->load(['announcements' => function ($query) {
            $query->active()
                ->orderByDesc('is_pinned')
                ->orderByDesc('published_at');
        }]);
        $riders = $track->riders()
            ->upcoming()
            ->with('clubModel')
            ->orderBy('ride_date')
            ->orderBy('ride_time')
            ->paginate(50, ['*'], 'riders_page')
            ->withQueryString();
        $comments = $track->comments()
            ->with('user')
            ->latest()
            ->paginate(25, ['*'], 'comments_page')
            ->withQueryString();

        return view('tracks.show', [
            'track' => $track,
            'riders' => $riders,
            'comments' => $comments,
            'clubs' => RiderChoices::clubs(),
            'categories' => RiderChoices::categories(),
            'experienceLevels' => RiderChoices::experienceLevels(),
            'rideDates' => Rider::bookableDates(),
        ]);
    }
}
