<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'care_worker_id',
        'track_coordinates',
        'address',
        'recorded_at',
        'synced',
        'visitation_id',
        'arrival_status',
    ];

    protected $casts = [
        'track_coordinates' => 'array',
        'recorded_at' => 'datetime',
        'synced' => 'boolean',
    ];

    // Relationships
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function careWorker()
    {
        return $this->belongsTo(User::class, 'care_worker_id');
    }

    public function visitation()
    {
        return $this->belongsTo(Visitation::class, 'visitation_id');
    }

    // Optional: Accessors for lat/lng
    public function getLatitudeAttribute()
    {
        return $this->track_coordinates['lat'] ?? null;
    }
    public function getLongitudeAttribute()
    {
        return $this->track_coordinates['lng'] ?? null;
    }
}
