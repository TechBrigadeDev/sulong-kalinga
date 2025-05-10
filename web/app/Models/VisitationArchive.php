<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitationArchive extends Model
{
    use HasFactory;
    
    protected $table = 'visitation_archives';
    protected $primaryKey = 'archive_id';
    public $timestamps = false; // We'll use archived_at instead
    
    protected $fillable = [
        'visitation_id',
        'original_visitation_id',
        'care_worker_id',
        'beneficiary_id',
        'visitation_date',
        'visit_type',
        'is_flexible_time',
        'start_time',
        'end_time',
        'notes',
        'status',
        'date_assigned',
        'assigned_by',
        'archived_at',
        'reason',
        'archived_by'
    ];
    
    protected $casts = [
        'visitation_date' => 'date',
        'date_assigned' => 'date',
        'is_flexible_time' => 'boolean',
        'archived_at' => 'datetime',
    ];
    
    /**
     * Get the original visitation this archive relates to
     */
    public function visitation()
    {
        return $this->belongsTo(Visitation::class, 'original_visitation_id', 'visitation_id');
    }
    
    /**
     * Get the care worker assigned to this archived visitation
     */
    public function careWorker()
    {
        return $this->belongsTo(User::class, 'care_worker_id', 'id');
    }
    
    /**
     * Get the beneficiary for this archived visitation
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id', 'beneficiary_id');
    }
    
    /**
     * Get the user who archived this visitation
     */
    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by', 'id');
    }
}