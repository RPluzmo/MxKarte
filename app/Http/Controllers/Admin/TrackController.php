<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
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
            'track' => new Track(),
            'owners' => User::whereIn('role', ['owner', 'admin'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTrack($request);
        $validated['slug'] = Str::slug($validated['slug']);

        Track::create($validated);

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
        $track->update($validated);

        return redirect()->route('admin.tracks.index')->with('status', 'Trase atjaunota.');
    }

    public function destroy(Track $track)
    {
        $track->delete();

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
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer', $ownerRule],
        ]);
    }
}
