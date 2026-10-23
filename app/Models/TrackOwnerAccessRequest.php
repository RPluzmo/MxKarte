<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackOwnerAccessRequest extends Model
{
    protected $fillable = [
        'user_id',
        'track_id',
        'message',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
