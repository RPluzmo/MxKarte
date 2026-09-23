<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackPreferenceController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'track_ids' => ['nullable', 'array'],
            'track_ids.*' => ['integer', 'exists:tracks,id'],
        ]);

        $request->user()->preferredTracks()->sync($validated['track_ids'] ?? []);

        return back()->with('status', 'Prioritārās trases saglabātas.');
    }
}
