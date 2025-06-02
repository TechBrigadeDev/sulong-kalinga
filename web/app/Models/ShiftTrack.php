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
        'latitude',
        'longitude',
        'address',
        'recorded_at',
        'synced',
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
}
