<?php
// app/Models/RecurringPattern.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringPattern extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'pattern_id';
    
    protected $fillable = [
        'appointment_id',
        'visitation_id',
        'pattern_type',
        'day_of_week',
        'recurrence_end'
    ];
    
    protected $casts = [
        'recurrence_end' => 'date',
    ];
    
    /**
     * Get the appointment associated with this recurring pattern
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }
    
    /**
     * Get the visitation associated with this recurring pattern
     */
    public function visitation()
    {
        return $this->belongsTo(Visitation::class, 'visitation_id', 'visitation_id');
    }
}