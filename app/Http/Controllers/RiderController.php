<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use App\Models\Track;
use App\Rules\PersonName;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RiderController extends Controller
{
    public function store(Request $request, Track $track)
    {
        if ($user = $request->user()) {
            if (blank($user->surname) || blank($user->category) || blank($user->experience_level)) {
                return redirect()
                    ->route('profile.edit')
                    ->with('status', 'Pirms pieteikšanās, lūdzu, pabeidz profilu: uzvārds, motocikla kategorija un pieredze.');
            }

            $request->merge([
                'name' => $user->name,
                'surname' => $user->surname,
                'club_id' => $user->club_id,
                'category' => $user->category,
                'experience_level' => $user->experience_level,
            ]);
        }

        $request->merge([
            'name' => PersonName::normalize($request->input('name')),
            'surname' => PersonName::normalize($request->input('surname')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', new PersonName(), 'max:255'],
            'surname' => ['required', 'string', new PersonName(), 'max:255'],
            'club_id' => ['nullable', 'integer', 'exists:clubs,id'],
            'category' => ['required', Rule::in(['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi'])],
            'experience_level' => ['required', Rule::in(['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis'])],
            'ride_date' => ['required', 'date_format:Y-m-d', Rule::in(array_keys(Rider::bookableDates()))],
            'ride_time' => ['required', 'date_format:H:i', 'after_or_equal:06:00', 'before_or_equal:23:59'],
        ]);

        if ($validated['ride_date'] === today()->toDateString() && $validated['ride_time'] <= now()->format('H:i')) {
            throw ValidationException::withMessages(['ride_time' => 'Šodien nevar pieteikties jau pagājušam laikam.']);
        }

        $registrationKey = Rider::registrationKeyFor([
            ...$validated,
            'track_id' => $track->id,
            'user_id' => $request->user()?->id,
        ]);

        if (Rider::where('registration_key', $registrationKey)->exists()) {
            throw ValidationException::withMessages([
                'ride_date' => 'Šim braucējam šajā trasē šajā dienā jau ir pieteikums.',
            ]);
        }

        try {
            $track->riders()->create([
                ...$validated,
                'registration_key' => $registrationKey,
                'track_id' => $track->id,
                'user_id' => $request->user()?->id,
            ]);
        } catch (QueryException $exception) {
            if (! Rider::where('registration_key', $registrationKey)->exists()) {
                throw $exception;
            }

            throw ValidationException::withMessages([
                'ride_date' => 'Šim braucējam šajā trasē šajā dienā jau ir pieteikums.',
            ]);
        }

        return redirect()
            ->route('tracks.show', $track);
    }
}
