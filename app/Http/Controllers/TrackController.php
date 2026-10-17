<?php

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function edit(Request $request, Track $track)
    {
        $this->authorize('update', $track);

        return view('tracks.edit', compact('track'));
    }

    public function update(Request $request, Track $track)
    {
        $this->authorize('update', $track);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'surface_type' => ['nullable', 'string', 'max:255'],
        ]);

        $track->update($validated);

        return redirect()
            ->route('tracks.show', $track)
            ->with('status', 'Trases informācija atjaunota.');
    }
}
