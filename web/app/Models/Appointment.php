<?php
// app/Models/Appointment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'appointment_id';
    
    protected $fillable = [
        'appointment_type_id',
        'title',
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
        'updated_by'
    ];
    
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_flexible_time' => 'boolean',
    ];
    
    /**
     * Get the appointment type
     */
    public function type()
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type_id', 'appointment_type_id');
    }
    
    /**
     * Get the participants of this appointment
     */
    public function participants()
    {
        return $this->hasMany(AppointmentParticipant::class, 'appointment_id', 'appointment_id');
    }
    
    /**
     * Get the creator of this appointment
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
    /**
     * Get the recurring pattern for this appointment if any
     */
    public function recurringPattern()
    {
        return $this->hasOne(RecurringPattern::class, 'appointment_id', 'appointment_id');
    }
    
    /**
     * Get all users participating in this appointment
     */
    public function userParticipants()
    {
        return $this->hasManyThrough(
            User::class,
            AppointmentParticipant::class,
            'appointment_id', // Foreign key on AppointmentParticipant
            'id', // Foreign key on User
            'appointment_id', // Local key on this model
            'participant_id' // Local key on AppointmentParticipant
        )->where('participant_type', 'cose_user');
    }
    
    /**
     * Get all beneficiaries participating in this appointment
     */
    public function beneficiaryParticipants()
    {
        return $this->hasManyThrough(
            Beneficiary::class,
            AppointmentParticipant::class,
            'appointment_id',
            'beneficiary_id',
            'appointment_id',
            'participant_id'
        )->where('participant_type', 'beneficiary');
    }
    
    /**
     * Get all family members participating in this appointment
     */
    public function familyParticipants()
    {
        return $this->hasManyThrough(
            FamilyMember::class,
            AppointmentParticipant::class,
            'appointment_id',
            'family_member_id',
            'appointment_id',
            'participant_id'
        )->where('participant_type', 'family_member');
    }
}