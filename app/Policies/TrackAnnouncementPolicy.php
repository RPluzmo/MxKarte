<?php

namespace App\Policies;

use App\Models\TrackAnnouncement;
use App\Models\User;

class TrackAnnouncementPolicy
{
    public function update(User $user, TrackAnnouncement $announcement): bool
    {
        return $user->role === 'owner'
            && (int) $announcement->track->user_id === (int) $user->id;
    }

    public function delete(User $user, TrackAnnouncement $announcement): bool
    {
        return $this->update($user, $announcement);
    }
}
