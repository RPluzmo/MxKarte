<?php

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function edit(Request $request, Track $track)
    {
        $this->authorizeOwner($request, $track);

        return view('tracks.edit', compact('track'));
    }

    public function update(Request $request, Track $track)
    {
        $this->authorizeOwner($request, $track);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $track->update($validated);

        return redirect()
            ->route('tracks.show', $track)
            ->with('status', 'Trases informācija atjaunota.');
    }

    private function authorizeOwner(Request $request, Track $track): void
    {
        abort_unless((int) $track->user_id === (int) $request->user()->id, 403);
    }
}