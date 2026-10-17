<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\TrackAnnouncement;
use Illuminate\Http\Request;

class TrackAnnouncementController extends Controller
{
    public function store(Request $request, Track $track)
    {
        $this->authorize('createAnnouncement', $track);

        $validated = $this->validateAnnouncement($request);

        $track->announcements()->create([
            ...$validated,
            'user_id' => $request->user()->id,
            'published_at' => now(),
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return back()->with('status', 'Trases paziņojums publicēts.');
    }

    public function update(Request $request, TrackAnnouncement $announcement)
    {
        $this->authorize('update', $announcement);

        $validated = $this->validateAnnouncement($request);
        $announcement->update([
            ...$validated,
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return back()->with('status', 'Trases paziņojums atjaunots.');
    }

    public function destroy(Request $request, TrackAnnouncement $announcement)
    {
        $this->authorize('delete', $announcement);
        $announcement->delete();

        return back()->with('status', 'Trases paziņojums izdzēsts.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_pinned' => ['sometimes', 'boolean'],
        ]);
    }
}
