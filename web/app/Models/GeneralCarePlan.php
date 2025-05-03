<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralCarePlan extends Model
{
    use HasFactory;

    protected $table = 'general_care_plans'; // Table name
    protected $primaryKey = 'general_care_plan_id'; // Explicitly set the primary key
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'care_worker_id', 'emergency_plan', 'review_date', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'review_date' => 'date',
    ];

    public function mobility()
    {
        return $this->hasOne(Mobility::class, 'general_care_plan_id');
    }

    public function cognitiveFunction()
    {
        return $this->hasOne(CognitiveFunction::class, 'general_care_plan_id');
    }

    public function emotionalWellbeing()
    {
        return $this->hasOne(EmotionalWellbeing::class, 'general_care_plan_id');
    }

    public function careWorkerResponsibility()
    {
        return $this->hasMany(CareWorkerResponsibility::class, 'general_care_plan_id');
    }

    public function healthHistory()
    {
        return $this->hasOne(HealthHistory::class, 'general_care_plan_id');
    }

    public function careNeeds()
    {
        return $this->hasMany(CareNeed::class, 'general_care_plan_id');
    }

    public function medications()
    {
        return $this->hasMany(Medication::class, 'general_care_plan_id');
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }
}