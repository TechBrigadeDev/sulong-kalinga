<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
     * 
     * @param Carbon $startDate The date being edited or start date
     * @param Carbon $endDate The end date for generation
     * @param array $occurrenceIds Array to store generated occurrence IDs
     */
    private function generateMonthlyOccurrences($startDate, $endDate, &$occurrenceIds)
    {
        // Get the exact date being edited and target day
        $editedDate = $startDate->format('Y-m-d');
        $targetDayOfMonth = $startDate->day;
        
        \Log::info("Monthly pattern: Starting with additional debugging", [
            'visitation_id' => $this->visitation_id,
            'edited_date' => $editedDate, 
            'target_day' => $targetDayOfMonth
        ]);
        
        // STEP 1: Get ALL existing occurrences for this visitation
        $allOccurrences = VisitationOccurrence::where('visitation_id', $this->visitation_id)
            ->get();
        
        // Group occurrences into different categories
        $pastOccurrences = [];
        $editedDateOccurrences = [];
        $wrongDayFutureOccurrences = [];
        
        // IMPORTANT FIX: Add additional logging for each occurrence to debug date matching
        foreach ($allOccurrences as $occurrence) {
            $occDate = Carbon::parse($occurrence->occurrence_date);
            $occDateStr = $occDate->format('Y-m-d');
            $occYear = $occDate->year;
            $occMonth = $occDate->month;
            $occDay = $occDate->day;
            
            \Log::info("Analyzing occurrence", [
                'occurrence_id' => $occurrence->occurrence_id,
                'raw_date' => $occurrence->occurrence_date,
                'parsed_date' => $occDateStr,
                'y-m-d' => "$occYear-$occMonth-$occDay",
                'edited_date' => $editedDate,
                'comparison' => ($occDateStr === $editedDate ? 'MATCH' : 'NO MATCH')
            ]);
            
            // IMPROVED COMPARISON: Compare year, month, day separately
            $editedDateTime = Carbon::parse($editedDate);
            
            // Is this the exact occurrence being edited?
            if ($occDate->isSameDay($editedDateTime)) {
                $editedDateOccurrences[] = $occurrence->occurrence_id;
                \Log::info("Found occurrence on edited date", [
                    'occurrence_id' => $occurrence->occurrence_id,
                    'date' => $occDateStr
                ]);
            }
            // Is this a future occurrence with the wrong day?
            elseif ($occDate > $startDate && $occDate->day !== $targetDayOfMonth) {
                $wrongDayFutureOccurrences[] = $occurrence->occurrence_id;
            }
            // Is this a past occurrence?
            elseif ($occDate < $startDate) {
                $pastOccurrences[] = $occurrence->occurrence_id;
            }
        }
        
        \Log::info("Occurrence analysis complete", [
            'visitation_id' => $this->visitation_id,
            'past_count' => count($pastOccurrences),
            'edited_date_count' => count($editedDateOccurrences),
            'future_wrong_day_count' => count($wrongDayFutureOccurrences),
            'all_occurrences' => $allOccurrences->count()
        ]);
        
        // DIRECT APPROACH: If no occurrences found, try with direct PostgreSQL date casting
        if (empty($editedDateOccurrences)) {
            $directSQLResult = DB::select(
                "SELECT occurrence_id FROM visitation_occurrences 
                WHERE visitation_id = ? 
                AND DATE(occurrence_date) = ?::date",
                [$this->visitation_id, $editedDate]
            );
            
            foreach ($directSQLResult as $result) {
                $editedDateOccurrences[] = $result->occurrence_id;
            }
            
            \Log::info("Direct SQL attempt to find occurrence", [
                'visitation_id' => $this->visitation_id,
                'edited_date' => $editedDate,
                'found_count' => count($editedDateOccurrences)
            ]);
        }
        
        // STEP 2: Delete edited date occurrences by ID
        if (!empty($editedDateOccurrences)) {
            $deleteCount = VisitationOccurrence::whereIn('occurrence_id', $editedDateOccurrences)->delete();
            
            \Log::info("Deleted occurrences on the edited date", [
                'visitation_id' => $this->visitation_id,
                'edited_date' => $editedDate,
                'occurrence_ids' => $editedDateOccurrences,
                'deleted_count' => $deleteCount
            ]);
        } else {
            // LAST RESORT: Use raw SQL to directly delete the occurrence
            $lastResortCount = DB::delete(
                "DELETE FROM visitation_occurrences 
                WHERE visitation_id = ? 
                AND DATE(occurrence_date) = ?::date",
                [$this->visitation_id, $editedDate]
            );
            
            \Log::info("Last resort direct SQL delete attempt", [
                'visitation_id' => $this->visitation_id,
                'edited_date' => $editedDate,
                'deleted_count' => $lastResortCount
            ]);
        }
        
        // STEP 3: Delete future occurrences with wrong day by ID
        if (!empty($wrongDayFutureOccurrences)) {
            $deleteCount = VisitationOccurrence::whereIn('occurrence_id', $wrongDayFutureOccurrences)->delete();
            
            \Log::info("Deleted future occurrences with wrong day", [
                'visitation_id' => $this->visitation_id,
                'deleted_count' => $deleteCount
            ]);
        }
        
        // STEP 4: Generate new occurrences from edited date forward
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            // Create new occurrence using a forced insert (not checking if exists)
            $occurrence = VisitationOccurrence::create([
                'visitation_id' => $this->visitation_id,
                'occurrence_date' => $currentDate->format('Y-m-d'),
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'is_flexible_time' => $this->is_flexible_time ?? false,
                'status' => $currentDate < now() ? 'completed' : 'scheduled'
            ]);
            
            if ($occurrence) {
                $occurrenceIds[] = $occurrence->occurrence_id;
                
                if ($currentDate->format('Y-m-d') === $editedDate) {
                    \Log::info("Force created occurrence for edited date", [
                        'visitation_id' => $this->visitation_id,
                        'date' => $currentDate->format('Y-m-d'),
                        'occurrence_id' => $occurrence->occurrence_id
                    ]);
                }
            }
            
            // Move to next month
            $currentDate->addMonth();
            
            // Handle edge cases like the 31st of the month
            $daysInMonth = $currentDate->daysInMonth;
            $currentDate->day = min($targetDayOfMonth, $daysInMonth);
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