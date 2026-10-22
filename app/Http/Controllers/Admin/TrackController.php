<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\User;
use App\Support\TrackImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TrackController extends Controller
{
    public function index()
    {
        $tracks = Track::with('owner')->latest()->paginate(25);

        return view('admin.tracks.index', compact('tracks'));
    }

    public function create()
    {
        return view('admin.tracks.form', [
            'track' => new Track,
            'owners' => User::whereIn('role', ['owner', 'admin'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTrack($request);
        $validated['slug'] = Str::slug($validated['slug']);

        DB::transaction(function () use ($validated, $request): void {
            $track = Track::create($this->trackAttributes($validated));
            app(TrackImageManager::class)->sync($track, $request, $validated);
        });

        return redirect()->route('admin.tracks.index')->with('status', 'Trase izveidota.');
    }

    public function edit(Track $track)
    {
        return view('admin.tracks.form', [
            'track' => $track,
            'owners' => User::whereIn('role', ['owner', 'admin'])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Track $track)
    {
        $validated = $this->validateTrack($request, $track);
        $validated['slug'] = Str::slug($validated['slug']);
        app(TrackImageManager::class)->sync(
            $track,
            $request,
            $validated,
            $this->trackAttributes($validated)
        );

        return redirect()->route('admin.tracks.index')->with('status', 'Trase atjaunota.');
    }

    public function destroy(Track $track)
    {
        app(TrackImageManager::class)->deleteTrack($track);

        return redirect()->route('admin.tracks.index')->with('status', 'Trase izdzēsta.');
    }

    private function validateTrack(Request $request, ?Track $track = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('name', '')),
        ]);

        $ownerRule = Rule::exists('users', 'id')->where(function ($query) {
            $query->whereIn('role', ['owner', 'admin']);
        });

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('tracks', 'slug')->ignore($track?->id)],
            'lat' => ['required', 'numeric', 'between:55.6,58.1'],
            'lng' => ['required', 'numeric', 'between:20.8,28.3'],
            'description' => ['nullable', 'string'],
            'surface_type' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', $ownerRule],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'remove_cover' => ['nullable', 'boolean'],
            'gallery_images' => ['nullable', 'array', 'max:4'],
            'gallery_images.*' => ['image', 'max:5120'],
            'remove_gallery_ids' => ['nullable', 'array'],
            'remove_gallery_ids.*' => ['integer', 'exists:track_images,id'],
        ]);
    }

    private function trackAttributes(array $validated): array
    {
        return array_intersect_key($validated, array_flip([
            'name', 'slug', 'lat', 'lng', 'description', 'surface_type', 'user_id',
        ]));
    }

}
