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
    public function generateOccurrences($monthsAhead = 3)
    {
        // If no recurring pattern, just create a single occurrence
        if (!$this->recurringPattern) {
            $this->createSingleOccurrence();
            return;
        }
        
        $pattern = $this->recurringPattern;
        $startDate = Carbon::parse($this->date);
        $endDate = $pattern->recurrence_end ?? $startDate->copy()->addMonths($monthsAhead);
        
        // Generate occurrences based on pattern type
        switch ($pattern->pattern_type) {
            case 'daily':
                $this->generateDailyOccurrences($startDate, $endDate);
                break;
                
            case 'weekly':
                $this->generateWeeklyOccurrences($startDate, $endDate, $pattern->day_of_week);
                break;
                
            case 'monthly':
                $this->generateMonthlyOccurrences($startDate, $endDate);
                break;
        }
        
        return true;
    }

    // Helper function for weekly occurrences - fix this method
    private function generateWeeklyOccurrences($startDate, $endDate, $dayOfWeek)
    {
        // If dayOfWeek contains commas, it means multiple days were selected
        $daysOfWeek = [];
        if (strpos($dayOfWeek, ',') !== false) {
            // Split the comma-separated string into an array of day numbers
            $daysOfWeek = array_map('intval', explode(',', $dayOfWeek));
        } else {
            // Single day as integer
            $daysOfWeek = [$dayOfWeek !== null ? intval($dayOfWeek) : $startDate->dayOfWeek];
        }
        
        $currentDate = $startDate->copy();
        
        // Create occurrences until the end date
        while ($currentDate <= $endDate) {
            foreach ($daysOfWeek as $day) {
                // Calculate the next occurrence of this day of the week
                $daysToAdd = ($day - $currentDate->dayOfWeek + 7) % 7;
                if ($daysToAdd > 0 || $currentDate->dayOfWeek !== $day) {
                    $occurrenceDate = $currentDate->copy()->addDays($daysToAdd);
                } else {
                    $occurrenceDate = $currentDate->copy();
                }
                
                // Only create occurrence if it's not before the start date and not after the end date
                if ($occurrenceDate >= $startDate && $occurrenceDate <= $endDate) {
                    $this->createOccurrence($occurrenceDate);
                }
            }
            
            // Move to the next week
            $currentDate->addWeek();
        }
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

    /**
     * Create a single occurrence for the given date
     * 
     * @param Carbon $date The date for the occurrence
     * @return AppointmentOccurrence The created occurrence
     */
    private function createOccurrence($date)
    {
        // Check if occurrence already exists for this date
        $existingOccurrence = $this->occurrences()
            ->whereDate('occurrence_date', $date)
            ->first();
        
        if ($existingOccurrence) {
            return $existingOccurrence;
        }
        
        // Check if this date has an exception
        $hasException = $this->exceptions()
            ->whereDate('exception_date', $date)
            ->exists();
        
        if ($hasException) {
            return null;
        }
        
        // Create new occurrence
        return AppointmentOccurrence::create([
            'appointment_id' => $this->appointment_id,
            'occurrence_date' => $date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_flexible_time' => $this->is_flexible_time,
            'status' => 'scheduled'
        ]);
    }

    /**
     * Create an occurrence for the appointment date
     */
    private function createSingleOccurrence()
    {
        // Use the appointment's own date
        return $this->createOccurrence(Carbon::parse($this->date));
    }

    /**
     * Generate daily occurrences for a recurring appointment
     * 
     * @param Carbon $startDate The start date for generating occurrences
     * @param Carbon $endDate The end date for generating occurrences
     * @return int Number of occurrences created
     */
    public function generateDailyOccurrences($startDate, $endDate)
    {
        if (!$this->recurringPattern) {
            return 0;
        }
        
        // Make sure we're working with Carbon instances
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);
        
        // Don't continue if end date is in the past
        if ($endDate->isBefore($startDate)) {
            return 0;
        }
        
        // Counter for created occurrences
        $count = 0;
        
        // Generate daily occurrences
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            // Create an occurrence for this date
            $occurrence = $this->createOccurrence($currentDate);
            if ($occurrence) {
                $count++;
            }
            
            // Move to the next day
            $currentDate->addDay();
        }
        
        return $count;
    }

    /**
     * Generate monthly occurrences for a recurring appointment
     * 
     * @param Carbon $startDate The start date for generating occurrences
     * @param Carbon $endDate The end date for generating occurrences
     * @return int Number of occurrences created
     */
    public function generateMonthlyOccurrences($startDate, $endDate)
    {
        if (!$this->recurringPattern) {
            return 0;
        }
        
        // Make sure we're working with Carbon instances
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);
        
        // Don't continue if end date is in the past
        if ($endDate->isBefore($startDate)) {
            return 0;
        }
        
        // Counter for created occurrences
        $count = 0;
        
        // Generate monthly occurrences - keep the same day of the month
        $dayOfMonth = $startDate->day;
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Create an occurrence for this date
            $occurrence = $this->createOccurrence($currentDate);
            if ($occurrence) {
                $count++;
            }
            
            // Try to move to the same day next month
            $nextMonth = $currentDate->copy()->addMonth();
            
            // Handle edge cases like Feb 30 becoming Mar 2/3
            // by setting the day explicitly to ensure we maintain the same day of month
            try {
                $currentDate = $nextMonth->setDay($dayOfMonth);
            } catch (\Exception $e) {
                // If the next month doesn't have this day (e.g., trying to get Feb 30),
                // use the last day of the next month instead
                $currentDate = $nextMonth->endOfMonth();
            }
        }
        
        return $count;
    }
}