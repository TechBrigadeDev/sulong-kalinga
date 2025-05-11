<?php
// app/Models/Appointment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    
    /**
     * Get all occurrences for this appointment
     */
    public function occurrences()
    {
        return $this->hasMany(AppointmentOccurrence::class, 'appointment_id', 'appointment_id');
    }
    
    /**
     * Get all exceptions for this appointment
     */
    public function exceptions()
    {
        return $this->hasMany(AppointmentException::class, 'appointment_id', 'appointment_id');
    }
    
    /**
     * Get the historical archive records for this appointment
     */
    public function archives()
    {
        return $this->hasMany(AppointmentArchive::class, 'original_appointment_id', 'appointment_id');
    }
    
    /**
     * Generate occurrences for this recurring appointment
     * 
     * @param int $months Number of months to generate occurrences for
     * @return array Array of generated occurrence IDs
     */
    public function generateOccurrences($months = 3)
    {
        // Only generate occurrences if this is a recurring appointment
        if (!$this->recurringPattern) {
            // For non-recurring, create a single occurrence
            $occurrence = AppointmentOccurrence::create([
                'appointment_id' => $this->appointment_id,
                'occurrence_date' => $this->date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'status' => $this->status
            ]);
            
            return [$occurrence->occurrence_id];
        }
        
        // For recurring appointments, generate multiple occurrences
        $pattern = $this->recurringPattern;
        $startDate = $this->date;
        $endDate = $pattern->recurrence_end ?? Carbon::now()->addMonths($months);
        
        // Use the earlier date between the specified end date and the pattern's end date
        if ($pattern->recurrence_end && $pattern->recurrence_end->lt($endDate)) {
            $endDate = $pattern->recurrence_end;
        }
        
        $occurrenceIds = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $occurrence = AppointmentOccurrence::create([
                'appointment_id' => $this->appointment_id,
                'occurrence_date' => $currentDate->format('Y-m-d'),
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'status' => $currentDate < Carbon::now() ? 'completed' : 'scheduled'
            ]);
            
            $occurrenceIds[] = $occurrence->occurrence_id;
            
            // Calculate next occurrence date based on pattern
            switch ($pattern->pattern_type) {
                case 'daily':
                    $currentDate->addDay();
                    break;
                case 'weekly':
                    $currentDate->addWeek();
                    break;
                case 'monthly':
                    $currentDate->addMonth();
                    break;
            }
        }
        
        return $occurrenceIds;
    }
    
    /**
     * Move this appointment to the archive table
     * 
     * @param string $reason The reason for archiving
     * @param int $archivedBy User ID who archived the record
     * @return AppointmentArchive The created archive record
     */
    public function archive($reason, $archivedBy)
    {
        return AppointmentArchive::create([
            'appointment_id' => $this->appointment_id,
            'original_appointment_id' => $this->appointment_id,
            'title' => $this->title,
            'appointment_type_id' => $this->appointment_type_id,
            'description' => $this->description,
            'other_type_details' => $this->other_type_details,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_flexible_time' => $this->is_flexible_time,
            'meeting_location' => $this->meeting_location,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'archived_at' => Carbon::now(),
            'reason' => $reason,
            'archived_by' => $archivedBy
        ]);
    }
}