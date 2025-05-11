<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentOccurrence extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'occurrence_id';
    
    protected $fillable = [
        'appointment_id',
        'occurrence_date',
        'start_time',
        'end_time',
        'status',
        'is_modified',
        'notes'
    ];
    
    protected $casts = [
        'occurrence_date' => 'date',
        'is_modified' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];
    
    /**
     * Get the appointment this occurrence belongs to
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }
}