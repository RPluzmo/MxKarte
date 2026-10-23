<?php

namespace App\Support;

use App\Models\Rider;
use App\Models\SiteAnnouncement;
use App\Models\TrackAnnouncement;
use App\Models\TrackComment;
use Illuminate\Support\Facades\Cache;

class ExpiredContentPruner
{
    public function prune(): array
    {
        $now = now();

        return [
            'Treniņu pieteikumi' => Rider::deleteExpired(),
            'Trases paziņojumi' => TrackAnnouncement::whereNotNull('expires_at')
                ->where('expires_at', '<=', $now)
                ->delete(),
            'Sistēmas paziņojumi' => SiteAnnouncement::whereNotNull('expires_at')
                ->where('expires_at', '<=', $now)
                ->delete(),
            'Komentāri (vecāki par 3 dienām)' => TrackComment::where('created_at', '<=', $now->copy()->subDays(3))
                ->delete(),
        ];
    }

    public function pruneOncePerDay(): ?array
    {
        $date = today()->toDateString();
        $key = 'expired-content-pruned-'.$date;

        $result = Cache::lock('expired-content-pruning-'.$date, 300)->get(function () use ($key): ?array {
            if (Cache::has($key)) {
                return null;
            }

            $result = $this->prune();
            Cache::put($key, true, now()->endOfDay());

            return $result;
        });

        return is_array($result) ? $result : null;
    }
}
