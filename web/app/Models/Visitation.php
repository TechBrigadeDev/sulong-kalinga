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
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $occurrence = VisitationOccurrence::create([
                'visitation_id' => $this->visitation_id,
                'occurrence_date' => $currentDate->format('Y-m-d'),
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'status' => $currentDate < now() ? 'completed' : 'scheduled'
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