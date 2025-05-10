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
        'acknowledgement_signature'
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