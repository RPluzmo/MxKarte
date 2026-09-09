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
    ];

     public function riders()
    {
        return $this->hasMany(Rider::class);
    }

}