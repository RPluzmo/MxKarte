<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Track;

class MapController extends Controller
{
    public function index()
    {
        $tracks = Track::select('id', 'name', 'lat', 'lng', 'description')->withCount('riders')->get();
        return view('map', ['tracks' => $tracks]);
    }

     public function show(Track $track)
    {
        $track->load('riders');
        $clubs = Club::orderBy('name')->get();

        return view('tracks.show', compact('track', 'clubs'));
    }

}
