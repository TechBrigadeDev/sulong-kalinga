<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareNeed extends Model
{
    use HasFactory;

    protected $table = 'care_needs'; // Table name
    protected $primaryKey = 'care_need_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'general_care_plan_id', 'care_category_id', 'frequency', 'assistance_required'
    ];

    public function careCategory()
    {
        return $this->hasOne(CareCategory::class, 'care_category_id');
    }

    // Get the care plan associated with the beneficiary.
    public function generalCarePlan()
    {
        return $this->belongsTo(GeneralCarePlan::class, 'general_care_plan_id');
    }  

    
}