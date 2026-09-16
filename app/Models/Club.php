<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function riders()
    {
        return $this->hasMany(Rider::class);
    }
}
