<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Track;
use App\Models\TrackComment;

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
        abort_unless(
            $comment->user_id === $request->user()->id
                || $comment->track->user_id === $request->user()->id
                || $request->user()->role === 'admin',
            403
        );

        $comment->delete();

        return back()->with('status', 'Komentārs izdzēsts.');
    }
}
