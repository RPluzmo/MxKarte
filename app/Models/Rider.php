<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

  class Rider extends Model
{
    protected $fillable = [
        'track_id',
        'user_id',
        'name',
        'surname',
        'club',
        'category',
        'experience_level',
        'ride_date',
        'ride_time',
    ];

    public const BOOKING_DAYS = 7;

    protected function casts(): array
    {
        return ['ride_date' => 'date'];
    }

    /** @return array<string, string> datums (Y-m-d) => nosaukums */
    public static function bookableDates(): array
    {
        $dates = [];

        for ($i = 0; $i < self::BOOKING_DAYS; $i++) {
            $date = now()->addDays($i)->locale('lv');
            $prefix = match ($i) {
                0 => 'Šodien, ',
                1 => 'Rīt, ',
                default => '',
            };

            $dates[$date->toDateString()] = $prefix . ucfirst($date->translatedFormat('l')) . ', ' . $date->format('d.m.Y');
        }

        return $dates;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('ride_date', '>=', today()->toDateString());
    }

    public static function deleteExpired(): int
    {
        return static::where('ride_date', '<', today()->toDateString())->delete();
    }

    /** Izpildās vienreiz dienā bez cron/schedule atbalsta. */
    public static function deleteExpiredOncePerDay(): void
    {
        if (Cache::add('riders-pruned-' . today()->toDateString(), true, now()->endOfDay())) {
            static::deleteExpired();
        }
    }

    public function getArrivalPeriodAttribute(): string
    {
        $hour = (int) substr($this->ride_time, 0, 2);

        return match (true) {
            $hour >= 6 && $hour <= 10 => 'Rīts',
            $hour >= 11 && $hour <= 14 => 'Pusdienlaiks',
            $hour >= 15 && $hour <= 17 => 'Pēcpusdiena',
            default => 'Vakars',
        };
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clubModel()
    {
        return $this->belongsTo(Club::class, 'club', 'name');
    }
}


