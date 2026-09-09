<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

  class Rider extends Model
{
    protected $fillable = [
        'track_id',
        'name',
        'surname',
        'club',
        'category',
        'experience_level',
        'ride_time',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}


