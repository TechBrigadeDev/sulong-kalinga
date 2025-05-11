<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentException extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'exception_id';
    
    protected $fillable = [
        'appointment_id',
        'exception_date',
        'status',
        'reason',
        'created_by'
    ];
    
    protected $casts = [
        'exception_date' => 'date'
    ];
    
    /**
     * Get the appointment this exception belongs to
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }
    
    /**
     * Get the user who created this exception
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}