<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackAnnouncement extends Model
{
     protected $fillable = [
        'track_id',
        'user_id',
        'title',
        'body',
        'published_at',
        'expires_at',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_pinned' => 'boolean',
        ];
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now())
            ->where(function (Builder $query)
            {$query->whereNull('expires_at')
            ->orWhere('expires_at', '>', now());});
    }
}

