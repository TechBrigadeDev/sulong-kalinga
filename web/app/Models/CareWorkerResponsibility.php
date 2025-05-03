<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareWorkerResponsibility extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'care_worker_responsibilities';

    // Define the primary key
    protected $primaryKey = 'cw_responsibility_id';

    public $timestamps = false; // Disable timestamps

    // Define the fillable attributes
    protected $fillable = [
        'general_care_plan_id',
        'care_worker_id',
        'task_description',
    ];

    // Define any relationships if applicable
    // For example, if a responsibility belongs to a care worker
    public function careWorker()
    {
        return $this->belongsTo(User::class, 'care_worker_id', 'id');
    }

    // Define any relationships if applicable
    // For example, if a responsibility belongs to a general care plan
    public function generalCarePlan()
    {
        return $this->belongsTo(GeneralCarePlan::class, 'general_care_plan_id');
    }
}