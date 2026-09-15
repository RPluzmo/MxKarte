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

}