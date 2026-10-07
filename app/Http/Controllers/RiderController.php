<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rider;
use App\Models\Track;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RiderController extends Controller
{
    public function store(Request $request, Track $track)
    {
        if ($user = $request->user()) {
            $request->merge([
                'name' => $user->name,
                'surname' => $user->surname,
                'club' => $user->club,
                'category' => $user->category,
                'experience_level' => $user->experience_level,
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'club' => ['nullable', 'string', 'exists:clubs,name'],
            'category' => ['required', Rule::in(['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi']),],
            'experience_level' => ['required', Rule::in(['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis']),],
            'ride_date' => ['required', 'date_format:Y-m-d', Rule::in(array_keys(Rider::bookableDates()))],
            'ride_time' => ['required', 'date_format:H:i', 'after_or_equal:06:00', 'before_or_equal:23:59'],
        ]);

        if ($validated['ride_date'] === today()->toDateString() && $validated['ride_time'] <= now()->format('H:i')) {
            throw ValidationException::withMessages(['ride_time' => 'Šodien nevar pieteikties jau pagājušam laikam.']);
        }

        $validated['club'] = $validated['club'] ?? 'Privāti';

        $track->riders()->create([
            ...$validated,
            'track_id' => $track->id,
            'user_id' => $request->user()?->id,
        ]);

        return redirect()
            ->route('tracks.show', $track);
    }
}
