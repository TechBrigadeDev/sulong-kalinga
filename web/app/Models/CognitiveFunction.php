<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CognitiveFunction extends Model
{
    use HasFactory;

    protected $table = 'cognitive_function'; // Table name
    protected $primaryKey = 'cognitive_function_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'general_care_plan_id', 'memory', 'thinking_skills', 'orientation', 'behavior'
    ];

    /**
     * Get the general care plan that owns this.
     */
    public function generalCarePlan()
    {
        return $this->belongsTo(GeneralCarePlan::class, 'general_care_plan_id');
    }

    
}