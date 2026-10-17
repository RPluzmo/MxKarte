<?php

namespace App\Policies;

use App\Models\TrackComment;
use App\Models\User;

class TrackCommentPolicy
{
    public function delete(User $user, TrackComment $comment): bool
    {
        return (int) $comment->user_id === (int) $user->id
            || (int) $comment->track->user_id === (int) $user->id
            || $user->role === 'admin';
    }
}
