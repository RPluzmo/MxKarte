<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rider;
use App\Models\Track;
use Illuminate\Validation\Rule;

class RiderController extends Controller
{
    public function store(Request $request, Track $track)
    {
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'club' => ['nullable', 'string', 'exists:clubs,name'],
            'category' => ['required', Rule::in(['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi']),],
            'experience_level' => ['required', Rule::in(['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis']),],
            'ride_time' => ['required', 'date_format:H:i'],
        ]);

        $track->riders()->create([
            ...$validated,
            'track_id' => $track->id,
            'user_id' => $request->user()?->id,
        ]);

        return redirect()
            ->route('tracks.show', $track);
    }
}
