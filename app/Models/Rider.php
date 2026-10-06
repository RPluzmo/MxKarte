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
        'club',
        'category',
        'experience_level',
        'ride_time',
    ];

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


