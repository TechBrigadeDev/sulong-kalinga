<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;
    
    // Define the table name
    protected $table = 'medications';

    // Define the primary key
    protected $primaryKey = 'medications_id';
    public $timestamps = false; // Disable timestamps

    // Define the fillable attributes
    protected $fillable = [
        'general_care_plan_id',
        'medication',
        'dosage',
        'frequency',
        'administration_instructions',
    ];

    // Define the relationship with the GeneralCarePlan model
    public function generalCarePlan()
    {
        return $this->belongsTo(GeneralCarePlan::class, 'general_care_plan_id');
    }
}