<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\TrackComment;
use Illuminate\Http\Request;

class TrackCommentController extends Controller
{
    public function store(Request $request, Track $track)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $track->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return back()->with('status', 'Komentārs publicēts.');
    }

    public function destroy(Request $request, TrackComment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', 'Komentārs izdzēsts.');
    }
}
