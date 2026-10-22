<?php

namespace App\Policies;

use App\Models\Track;
use App\Models\User;

class TrackPolicy
{
    public function update(User $user, Track $track): bool
    {
        return $user->role === 'owner'
            && (int) $track->user_id === (int) $user->id;
    }

    public function createAnnouncement(User $user, Track $track): bool
    {
        return $this->update($user, $track);
    }
}
