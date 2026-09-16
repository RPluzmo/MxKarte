<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rider;
use App\Models\Track;

class RiderController extends Controller
{
    public function store(Request $request, Track $track)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'club' => ['nullable', 'string', 'exists:clubs,name'],
            'category' => ['required', 'string', 'max:255'],
            'experience_level' => ['required', 'string', 'max:255'],
            'ride_time' => ['required', 'date_format:H:i'],
        ]);

        $request->user()->riders()->create([
            ...$validated,
            'track_id' => $track->id,
        ]);


        return redirect()
            ->route('tracks.show', $track);
    }
}
