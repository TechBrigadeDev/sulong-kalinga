<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitationOccurrence extends Model
{
    use HasFactory;
    
    protected $table = 'visitation_occurrences';
    protected $primaryKey = 'occurrence_id';
    
    protected $fillable = [
        'visitation_id',
        'occurrence_date',
        'start_time',
        'end_time',
        'status',
        'is_modified',
        'notes'
    ];
    
    protected $casts = [
        'occurrence_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_modified' => 'boolean',
    ];
    
    /**
     * Get the parent visitation for this occurrence
     */
    public function visitation()
    {
        return $this->belongsTo(Visitation::class, 'visitation_id', 'visitation_id');
    }
    
    /**
     * Mark this occurrence as completed
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
        
        return $this;
    }
    
    /**
     * Mark this occurrence as canceled
     */
    public function cancel($reason = null)
    {
        $this->status = 'canceled';
        if ($reason) {
            $this->notes = $reason;
        }
        $this->save();
        
        return $this;
    }
    
    /**
     * Scope query to only include future occurrences
     */
    public function scopeFuture($query)
    {
        return $query->where('occurrence_date', '>=', now()->format('Y-m-d'));
    }
    
    /**
     * Scope query to only include past occurrences
     */
    public function scopePast($query)
    {
        return $query->where('occurrence_date', '<', now()->format('Y-m-d'));
    }
}