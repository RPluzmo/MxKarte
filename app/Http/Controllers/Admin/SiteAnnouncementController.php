<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteAnnouncement;
use Illuminate\Http\Request;

class SiteAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = SiteAnnouncement::with('user')
            ->latest('published_at')
            ->paginate(25);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.form', [
            'announcement' => new SiteAnnouncement(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAnnouncement($request);

        SiteAnnouncement::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'published_at' => now(),
        ]);

        return redirect()->route('admin.announcements.index')->with('status', 'Admin ziņojums publicēts.');
    }

    public function edit(SiteAnnouncement $announcement)
    {
        return view('admin.announcements.form', compact('announcement'));
    }

    public function update(Request $request, SiteAnnouncement $announcement)
    {
        $validated = $this->validateAnnouncement($request);
        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')->with('status', 'Admin ziņojums atjaunots.');
    }

    public function destroy(SiteAnnouncement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('status', 'Admin ziņojums izdzēsts.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'expires_at' => ['nullable', 'date'],
        ]);
    }
}
