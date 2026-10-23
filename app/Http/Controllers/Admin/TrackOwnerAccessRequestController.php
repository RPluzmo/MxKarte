<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\TrackOwnerAccessRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TrackOwnerAccessRequestController extends Controller
{
    public function index()
    {
        $requests = TrackOwnerAccessRequest::with(['user', 'track', 'reviewer'])
            ->latest()
            ->paginate(20);

        return view('admin.owner-requests.index', compact('requests'));
    }

    public function approve(TrackOwnerAccessRequest $ownerAccessRequest): RedirectResponse
    {
        $assignedElsewhere = DB::transaction(function () use ($ownerAccessRequest): bool {
            $accessRequest = TrackOwnerAccessRequest::whereKey($ownerAccessRequest->id)->lockForUpdate()->firstOrFail();
            abort_unless($accessRequest->status === 'pending', 409, 'Šis pieteikums jau ir izskatīts.');

            $track = Track::whereKey($accessRequest->track_id)->lockForUpdate()->firstOrFail();
            if ($track->user_id !== null && (int) $track->user_id !== (int) $accessRequest->user_id) {
                return true;
            }

            $user = $accessRequest->user()->lockForUpdate()->firstOrFail();
            abort_if($user->role === 'admin', 409, 'Administratora kontam nevar piešķirt trases saimnieka lomu.');
            $user->update(['role' => 'owner']);
            $track->update(['user_id' => $user->id]);
            $accessRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            return false;
        });

        if ($assignedElsewhere) {
            return back()->withErrors(['request' => 'Trase jau ir piešķirta citam lietotājam. Pieteikums palika neizskatīts.']);
        }

        return back()->with('status', 'Pieteikums apstiprināts; lietotājam piešķirta saimnieka loma un trase.');
    }

    public function reject(TrackOwnerAccessRequest $ownerAccessRequest): RedirectResponse
    {
        DB::transaction(function () use ($ownerAccessRequest): void {
            $accessRequest = TrackOwnerAccessRequest::whereKey($ownerAccessRequest->id)->lockForUpdate()->firstOrFail();
            abort_unless($accessRequest->status === 'pending', 409, 'Šis pieteikums jau ir izskatīts.');

            $accessRequest->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('status', 'Pieteikums noraidīts.');
    }
}
