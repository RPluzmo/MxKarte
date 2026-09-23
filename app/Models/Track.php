<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'name',
        'lat',
        'lng',
        'description',
        'user_id',
    ];

     public function riders()
    {
        return $this->hasMany(Rider::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function comments()
    {
        return $this->hasMany(TrackComment::class);
    }
    
    public function announcements()
    {
        return $this->hasMany(TrackAnnouncement::class);
    }

    public function preferredByUsers()
    {
        return $this->belongsToMany(User::class, 'user_track_preferences')
            ->withTimestamps();
    }

}