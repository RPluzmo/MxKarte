<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    protected $fillable = [
        'track_id',
        'user_id',
        'name',
        'surname',
        'club_id',
        'category',
        'experience_level',
        'ride_date',
        'ride_time',
        'registration_key',
    ];

    public const BOOKING_DAYS = 7;

    protected function casts(): array
    {
        return ['ride_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (Rider $rider): void {
            if (! $rider->registration_key) {
                $rider->registration_key = self::registrationKeyFor($rider->getAttributes());
            }
        });
    }

    public static function registrationKeyFor(array $attributes): string
    {
        $identity = ! empty($attributes['user_id'])
            ? 'user:'.$attributes['user_id']
            : 'guest:'.mb_strtolower(trim($attributes['name'] ?? '')).':'.mb_strtolower(trim($attributes['surname'] ?? ''));

        return hash('sha256', implode('|', [
            $attributes['track_id'] ?? '',
            $attributes['ride_date'] instanceof \DateTimeInterface
                ? $attributes['ride_date']->format('Y-m-d')
                : substr((string) ($attributes['ride_date'] ?? ''), 0, 10),
            $identity,
        ]));
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

            $dates[$date->toDateString()] = $prefix.ucfirst($date->translatedFormat('l')).', '.$date->format('d.m.Y');
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
        return $this->belongsTo(Club::class, 'club_id');
    }
}
