<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalSigns extends Model
{
    use HasFactory;

    protected $table = 'vital_signs';
    protected $primaryKey = 'vital_signs_id';
    
    // Add created_by to the fillable array
    protected $fillable = [
        'blood_pressure',
        'body_temperature',
        'pulse_rate',
        'respiratory_rate',
        'created_by'  // Added this line to fix the error
    ];
    
    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
    public function weeklyCarePlan()
    {
        return $this->hasOne(WeeklyCarePlan::class, 'vital_signs_id', 'vital_signs_id');
    }
}