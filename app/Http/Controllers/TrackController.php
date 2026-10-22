<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Support\TrackImageManager;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function edit(Request $request, Track $track)
    {
        $this->authorize('update', $track);
        $track->load('images');

        return view('tracks.edit', compact('track'));
    }

    public function update(Request $request, Track $track)
    {
        $this->authorize('update', $track);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'surface_type' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'remove_cover' => ['nullable', 'boolean'],
            'gallery_images' => ['nullable', 'array', 'max:4'],
            'gallery_images.*' => ['image', 'max:5120'],
            'remove_gallery_ids' => ['nullable', 'array'],
            'remove_gallery_ids.*' => ['integer', 'exists:track_images,id'],
        ]);

        app(TrackImageManager::class)->sync(
            $track,
            $request,
            $validated,
            array_intersect_key($validated, array_flip(['name', 'description', 'surface_type']))
        );

        return redirect()
            ->route('tracks.show', $track)
            ->with('status', 'Trases informācija un attēli atjaunoti.');
    }
}
