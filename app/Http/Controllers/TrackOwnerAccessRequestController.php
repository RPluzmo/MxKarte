<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\TrackOwnerAccessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TrackOwnerAccessRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = TrackOwnerAccessRequest::with('track')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('owner-requests.index', compact('requests'));
    }

    public function create(Request $request)
    {
        abort_if($request->user()->role === 'admin', 403);

        $tracks = Track::whereNull('user_id')->orderBy('name')->get();

        return view('owner-requests.create', compact('tracks'));
    }

    public function store(Request $request)
    {
        abort_if($request->user()->role === 'admin', 403);

        $validated = $request->validate([
            'track_id' => ['required', 'integer', Rule::exists('tracks', 'id')->whereNull('user_id')],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
        ]);

        $created = DB::transaction(function () use ($request, $validated): bool {
            $track = Track::whereKey($validated['track_id'])->lockForUpdate()->firstOrFail();
            if ($track->user_id !== null) {
                return false;
            }

            $alreadyPending = TrackOwnerAccessRequest::where('user_id', $request->user()->id)
                ->where('track_id', $track->id)
                ->where('status', 'pending')
                ->exists();

            if ($alreadyPending) {
                return false;
            }

            TrackOwnerAccessRequest::create([
                'user_id' => $request->user()->id,
                'track_id' => $track->id,
                'message' => $validated['message'],
            ]);

            return true;
        });

        if (! $created) {
            return back()->withErrors(['track_id' => 'Šai trasei jau ir saimnieks vai arī par to jau ir neizskatīts pieteikums.'])->withInput();
        }

        return redirect()->route('owner-requests.index')->with('status', 'Pieteikums nosūtīts administratoram izvērtēšanai.');
    }
}
