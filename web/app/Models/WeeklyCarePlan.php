<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyCarePlan extends Model
{
    use HasFactory;

    protected $table = 'weekly_care_plans'; // Table name
    protected $primaryKey = 'weekly_care_plan_id'; // Explicitly set the primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'beneficiary_id', 'care_worker_id', 'care_manager_id', 'vital_signs_id', 'date', 
        'assessment', 'illnesses', 'evaluation_recommendations', 'photo_path',
        'created_by', 'updated_by', 'acknowledged_by_beneficiary', 'acknowledged_by_family',
        'acknowledgement_signature',
        'assessment_summary_draft',
        'assessment_translation_draft',
        'evaluation_summary_draft',
        'evaluation_translation_draft',
        'assessment_summary_sections',
        'assessment_translation_sections',
        'evaluation_translation_sections',
        'evaluation_summary_sections',
        'assessment_summary_final',
        'evaluation_summary_final',
        'has_ai_summary'
    ];

    protected $casts = [
        'assessment_summary_sections' => 'array',
        'evaluation_summary_sections' => 'array',
        'assessment_translation_sections' => 'array', // Add this line
        'evaluation_translation_sections' => 'array', // Add this line
        'has_ai_summary' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id', 'beneficiary_id');
    }
    
    public function careWorker()
    {
        return $this->belongsTo(User::class, 'care_worker_id', 'id');
    }

    public function careManager()
    {
        return $this->belongsTo(User::class, 'care_manager_id', 'id');
    }
    
    public function vitalSigns()
    {
        return $this->belongsTo(VitalSigns::class, 'vital_signs_id', 'vital_signs_id');
    }
    
    public function interventions()
    {
        return $this->hasMany(WeeklyCarePlanInterventions::class, 'weekly_care_plan_id', 'weekly_care_plan_id');
    }

    public function acknowledgedByBeneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'acknowledged_by_beneficiary');
    }

    public function acknowledgedByFamily()
    {
        return $this->belongsTo(FamilyMember::class, 'acknowledged_by_family');
    }
}