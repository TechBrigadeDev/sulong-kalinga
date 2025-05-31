<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'care_worker_id',
        'time_in',
        'time_out',
        'status',
    ];

    // Relationships
    public function careWorker()
    {
        return $this->belongsTo(User::class, 'care_worker_id');
    }

    public function tracks()
    {
        return $this->hasMany(ShiftTrack::class);
    }
}
