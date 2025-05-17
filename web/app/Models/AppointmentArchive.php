<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentArchive extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'archive_id';
    public $timestamps = false;
    
    protected $fillable = [
        'appointment_id',
        'original_appointment_id',
        'title',
        'appointment_type_id',
        'description',
        'other_type_details',
        'date',
        'start_time',
        'end_time',
        'is_flexible_time',
        'meeting_location',
        'status',
        'notes',
        'created_by',
        'archived_at',
        'reason',
        'archived_by'
    ];
    
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_flexible_time' => 'boolean',
        'archived_at' => 'datetime'
    ];
    
    /**
     * Get the appointment type
     */
    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type_id', 'appointment_type_id');
    }
    
    /**
     * Get the user who created this appointment
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
    /**
     * Get the user who archived this appointment
     */
    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by', 'id');
    }
}