<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitationException extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'exception_id';
    
    protected $fillable = [
        'visitation_id',
        'exception_date',
        'status',
        'reason',
        'created_by',
    ];
    
    protected $casts = [
        'exception_date' => 'date',
    ];
    
    /**
     * Get the visitation this exception belongs to
     */
    public function visitation()
    {
        return $this->belongsTo(Visitation::class, 'visitation_id', 'visitation_id');
    }
    
    /**
     * Get the user who created this exception
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}