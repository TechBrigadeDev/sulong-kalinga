<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitation extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'visitation_id';
    
    protected $fillable = [
        'care_worker_id',
        'beneficiary_id',
        'visit_type',
        'visitation_date',
        'is_flexible_time',
        'start_time',
        'end_time',
        'notes',
        'date_assigned',
        'assigned_by',
        'status',
        'confirmed_by_beneficiary',
        'confirmed_by_family',
        'confirmed_on',
        'work_shift_id',
        'visit_log_id'
    ];
    
    protected $casts = [
        'visitation_date' => 'date',
        'date_assigned' => 'date',
        'is_flexible_time' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'confirmed_on' => 'datetime'
    ];
    
    /**
     * Get the care worker assigned to this visitation
     */
    public function careWorker()
    {
        return $this->belongsTo(User::class, 'care_worker_id', 'id');
    }
    
    /**
     * Get the beneficiary for this visitation
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id', 'beneficiary_id');
    }
    
    /**
     * Get the recurring pattern for this visitation if any
     */
    public function recurringPattern()
    {
        return $this->hasOne(RecurringPattern::class, 'visitation_id', 'visitation_id');
    }
    
    /**
     * Get the historical archive records for this visitation
     */
    public function archives()
    {
        return $this->hasMany(VisitationArchive::class, 'original_visitation_id', 'visitation_id');
    }
    
    /**
     * Get all occurrences for this visitation
     */
    public function occurrences()
    {
        return $this->hasMany(VisitationOccurrence::class, 'visitation_id', 'visitation_id');
    }

    public function exceptions()
    {
        return $this->hasMany(VisitationException::class, 'visitation_id', 'visitation_id');
    }
        
    /**
     * Generate occurrences for this recurring visitation
     * 
     * @param int $months Number of months to generate occurrences for
     * @return array Array of generated occurrence IDs
     */
    public function generateOccurrences($months = 3)
    {
        // Only generate occurrences if this is a recurring visitation
        if (!$this->recurringPattern) {
            // For non-recurring, create a single occurrence
            $occurrence = VisitationOccurrence::create([
                'visitation_id' => $this->visitation_id,
                'occurrence_date' => $this->visitation_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'status' => $this->status
            ]);
            
            return [$occurrence->occurrence_id];
        }
        
        // For recurring appointments, generate multiple occurrences
        $pattern = $this->recurringPattern;
        $startDate = $this->visitation_date;
        $endDate = $pattern->recurrence_end ?? now()->addMonths($months);
        
        // Use the earlier date between the specified end date and the pattern's end date
        if ($pattern->recurrence_end && $pattern->recurrence_end->lt($endDate)) {
            $endDate = $pattern->recurrence_end;
        }
        
        $occurrenceIds = [];
        
        // Generate based on pattern type
        switch ($pattern->pattern_type) {
            case 'daily':
                $this->generateDailyOccurrences($startDate, $endDate, $occurrenceIds);
                break;
                
            case 'weekly':
                $this->generateWeeklyOccurrences($startDate, $endDate, $pattern->day_of_week, $occurrenceIds);
                break;
                
            case 'monthly':
                $this->generateMonthlyOccurrences($startDate, $endDate, $occurrenceIds);
                break;
        }
        
        return $occurrenceIds;
    }

    /**
     * Generate daily occurrences
     */
    private function generateDailyOccurrences($startDate, $endDate, &$occurrenceIds)
    {
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $occurrence = $this->createOccurrence($currentDate);
            if ($occurrence) {
                $occurrenceIds[] = $occurrence->occurrence_id;
            }
            $currentDate->addDay();
        }
    }

    /**
     * Generate weekly occurrences, handling multiple days of week
     */
    private function generateWeeklyOccurrences($startDate, $endDate, $dayOfWeek, &$occurrenceIds)
    {
        // Parse day_of_week string into array of integers
        $daysOfWeek = [];
        if (strpos($dayOfWeek, ',') !== false) {
            // Split the comma-separated string into an array of day numbers
            $daysOfWeek = array_map('intval', explode(',', $dayOfWeek));
        } else {
            // Single day as integer
            $daysOfWeek = [$dayOfWeek !== null ? intval($dayOfWeek) : $startDate->dayOfWeek];
        }
        
        $currentDate = clone $startDate;
        
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
                    $occurrence = $this->createOccurrence($occurrenceDate);
                    if ($occurrence) {
                        $occurrenceIds[] = $occurrence->occurrence_id;
                    }
                }
            }
            
            // Move to the next week
            $currentDate->addWeek();
        }
    }

    /**
     * Generate monthly occurrences
     */
    private function generateMonthlyOccurrences($startDate, $endDate, &$occurrenceIds)
    {
        $currentDate = clone $startDate;
        $dayOfMonth = $currentDate->day;
        
        while ($currentDate <= $endDate) {
            $occurrence = $this->createOccurrence($currentDate);
            if ($occurrence) {
                $occurrenceIds[] = $occurrence->occurrence_id;
            }
            
            // Move to next month
            $currentDate->addMonth();
            
            // Handle edge cases like the 31st of the month
            $daysInMonth = $currentDate->daysInMonth;
            $currentDate->day = min($dayOfMonth, $daysInMonth);
        }
    }

    /**
     * Create a single occurrence for a specific date
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
        return VisitationOccurrence::create([
            'visitation_id' => $this->visitation_id,
            'occurrence_date' => $date->format('Y-m-d'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_flexible_time' => $this->is_flexible_time ?? false,
            'status' => $date < now() ? 'completed' : 'scheduled'
        ]);
    }
    
    /**
     * Move this visitation to the archive table
     * 
     * @param string $reason The reason for archiving
     * @param int $archivedBy User ID who archived the record
     * @return VisitationArchive The created archive record
     */
    public function archive($reason, $archivedBy)
    {
        return VisitationArchive::create([
            'visitation_id' => $this->visitation_id,
            'original_visitation_id' => $this->visitation_id,
            'care_worker_id' => $this->care_worker_id,
            'beneficiary_id' => $this->beneficiary_id,
            'visitation_date' => $this->visitation_date,
            'visit_type' => $this->visit_type,
            'is_flexible_time' => $this->is_flexible_time,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'notes' => $this->notes,
            'status' => $this->status,
            'date_assigned' => $this->date_assigned,
            'assigned_by' => $this->assigned_by,
            'archived_at' => now(),
            'reason' => $reason,
            'archived_by' => $archivedBy
        ]);
    }
}