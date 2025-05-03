<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthHistory extends Model
{
    use HasFactory;

    protected $table = 'health_histories'; // Table name
    protected $primaryKey = 'health_history_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'general_care_plan_id', 'medical_conditions', 'medications', 'allergies', 'immunizations'
    ];

    /**
     * Get the general care plan that owns the health history.
     */
    public function generalCarePlan()
    {
        return $this->belongsTo(GeneralCarePlan::class, 'general_care_plan_id');
    }

}