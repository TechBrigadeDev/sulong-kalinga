<?php
// app/Models/AppointmentParticipant.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentParticipant extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'appointment_participant_id';
    
    protected $fillable = [
        'appointment_id',
        'participant_type',
        'participant_id',
        'is_organizer'
    ];
    
    protected $casts = [
        'is_organizer' => 'boolean',
    ];
    
    /**
     * Get the appointment this participant belongs to
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }
    
    /**
     * Get the participant (polymorphic)
     */
    public function participant()
    {
        if ($this->participant_type === 'cose_user') {
            return $this->belongsTo(User::class, 'participant_id', 'id');
        } elseif ($this->participant_type === 'beneficiary') {
            return $this->belongsTo(Beneficiary::class, 'participant_id', 'beneficiary_id');
        } elseif ($this->participant_type === 'family_member') {
            return $this->belongsTo(FamilyMember::class, 'participant_id', 'family_member_id');
        }
        
        return null;
    }
}