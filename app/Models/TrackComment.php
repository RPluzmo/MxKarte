<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackComment extends Model
{
    protected $fillable = [
        'track_id',
        'user_id',
        'body',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
