<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobility extends Model
{
    use HasFactory;

    protected $table = 'mobility'; // Table name
    protected $primaryKey = 'mobility_id'; // Explicitly set the primary key
    public $timestamps = false; // Disable timestamps
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'general_care_plan_id', 'walking_ability', 'assistive_devices', 'transportation_needs'
    ];

    /**
     * Get the general care plan that owns this.
     */
    public function generalCarePlan()
    {
        return $this->belongsTo(GeneralCarePlan::class, 'general_care_plan_id');
    }

}