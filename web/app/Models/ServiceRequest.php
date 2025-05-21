<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'service_request_id';
    
    protected $fillable = [
        'sender_id',
        'sender_type',
        'beneficiary_id',
        'service_type_id',
        'care_worker_id',
        'service_date',
        'service_time',
        'message',
        'status',
        'read_status',
        'read_at',
        'action_type',
        'action_taken_by',
        'action_taken_at',
    ];
    
    protected $casts = [
        'read_status' => 'boolean',
        'read_at' => 'datetime',
        'service_date' => 'date',
        'action_taken_at' => 'datetime',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function sender()
    {
        if ($this->sender_type === 'beneficiary') {
            return $this->belongsTo(Beneficiary::class, 'sender_id');
        } else {
            return $this->belongsTo(FamilyMember::class, 'sender_id');
        }
    }

    public function careWorker()
    {
        return $this->belongsTo(User::class, 'care_worker_id');
    }

    public function actionTakenBy()
    {
        return $this->belongsTo(User::class, 'action_taken_by');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceRequestType::class, 'service_type_id');
    }

    public function updates()
    {
        return $this->hasMany(ServiceRequestUpdate::class, 'service_request_id')->orderBy('created_at', 'desc');
    }
}