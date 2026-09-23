<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Rider;
use App\Models\Track;
use App\Models\TrackAnnouncement;

class MapController extends Controller
{
    public function index()
    {
        $tracks = Track::select('id', 'name', 'lat', 'lng', 'description')->withCount('riders')->get();
        $announcementQuery = TrackAnnouncement::with('track')->active();
        $announcementSearch = trim((string) request('announcement_search', ''));
        $announcementTrackId = request()->integer('track_id') ?: null;
        $onlyPinned = request()->boolean('only_pinned');

        if ($announcementSearch !== '') {
            $announcementQuery->where(function ($query) use ($announcementSearch) {
                $query->where('title', 'like', "%{$announcementSearch}%")
                    ->orWhere('body', 'like', "%{$announcementSearch}%");
            });
        }

        if ($announcementTrackId) {
            $announcementQuery->where('track_id', $announcementTrackId);
        }

        if ($onlyPinned) {
            $announcementQuery->where('is_pinned', true);
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
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->paginate(10)
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
            'clubTrackIds',
            'clubName',
            'announcements',
            'announcementSearch',
            'announcementTrackId',
            'onlyPinned',
            'preferredTrackIds'
        ));
    }

     public function show(Track $track)
    {
        $track->load(['riders', 'comments.user']);
        $track->load(['announcements' => function ($query) {
            $query->active()
                ->orderByDesc('is_pinned')
                ->orderByDesc('published_at');
        }]);
        $clubs = Club::orderBy('name')->get();

        return view('tracks.show', compact('track', 'clubs'));
    }

}
