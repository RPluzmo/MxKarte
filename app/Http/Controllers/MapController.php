<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Rider;
use App\Models\Track;

class MapController extends Controller
{
    public function index()
    {
        $tracks = Track::select('id', 'name', 'lat', 'lng', 'description')->withCount('riders')->get();
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

        return view('map', compact('tracks', 'clubTrackIds', 'clubName'));
    }

     public function show(Track $track)
    {
        $track->load('riders');
        $clubs = Club::orderBy('name')->get();

        return view('tracks.show', compact('track', 'clubs'));
    }

}
