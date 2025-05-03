<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyCarePlanInterventions extends Model
{
    use HasFactory;

    protected $table = 'weekly_care_plan_interventions'; // Table name
    protected $primaryKey = 'wcp_intervention_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'weekly_care_plan_id', 'intervention_id', 'care_category_id', 'intervention_description', 'duration_minutes', 'implemented'
    ];
}