<?php
// app/Models/MedicationSchedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationSchedule extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'medication_schedule_id';
    
    protected $fillable = [
        'beneficiary_id',
        'medication_name',
        'dosage',
        'medication_type',
        'morning_time',
        'noon_time',
        'evening_time',
        'night_time',
        'as_needed',
        'with_food_morning',
        'with_food_noon',
        'with_food_evening',
        'with_food_night',
        'special_instructions',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'updated_by'
    ];
    
    protected $casts = [
        'morning_time' => 'datetime',
        'noon_time' => 'datetime',
        'evening_time' => 'datetime',
        'night_time' => 'datetime',
        'as_needed' => 'boolean',
        'with_food_morning' => 'boolean',
        'with_food_noon' => 'boolean',
        'with_food_evening' => 'boolean',
        'with_food_night' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    /**
     * Get the beneficiary for this medication schedule
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id', 'beneficiary_id');
    }
    
    /**
     * Get the user who created this medication schedule
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
    /**
     * Get the user who last updated this medication schedule
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}