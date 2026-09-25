<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TrackImage extends Model
{
    protected $fillable = [
        'track_id',
        'type',
        'path',
        'sort_order',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

}
