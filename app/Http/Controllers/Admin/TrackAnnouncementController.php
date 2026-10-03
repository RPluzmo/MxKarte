<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\TrackAnnouncement;
use Illuminate\Http\Request;

class TrackAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = TrackAnnouncement::with(['track', 'user'])
            ->latest('published_at')
            ->paginate(25);

        return view('admin.track-announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.track-announcements.form', [
            'announcement' => new TrackAnnouncement(),
            'tracks' => Track::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAnnouncement($request);

        TrackAnnouncement::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'published_at' => now(),
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return redirect()->route('admin.track-announcements.index')
            ->with('status', 'Trases paziņojums publicēts.');
    }

    public function edit(TrackAnnouncement $trackAnnouncement)
    {
        return view('admin.track-announcements.form', [
            'announcement' => $trackAnnouncement,
            'tracks' => Track::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, TrackAnnouncement $trackAnnouncement)
    {
        $validated = $this->validateAnnouncement($request);
        $trackAnnouncement->update([
            ...$validated,
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return redirect()->route('admin.track-announcements.index')
            ->with('status', 'Trases paziņojums atjaunots.');
    }

    public function destroy(TrackAnnouncement $trackAnnouncement)
    {
        $trackAnnouncement->delete();

        return redirect()->route('admin.track-announcements.index')
            ->with('status', 'Trases paziņojums izdzēsts.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'track_id' => ['required', 'integer', 'exists:tracks,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'expires_at' => ['nullable', 'date'],
            'is_pinned' => ['sometimes', 'boolean'],
        ]);
    }
}
