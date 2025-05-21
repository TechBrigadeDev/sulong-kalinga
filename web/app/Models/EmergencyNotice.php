<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyNotice extends Model
{
    use HasFactory;

    protected $primaryKey = 'notice_id';
    
    protected $fillable = [
        'sender_id',
        'sender_type',
        'beneficiary_id',
        'emergency_type_id',
        'message',
        'status',
        'read_status',
        'read_at',
        'assigned_to',
        'action_type',
        'action_taken_by',
        'action_taken_at',
    ];
    
    protected $casts = [
        'read_status' => 'boolean',
        'read_at' => 'datetime',
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

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function actionTakenBy()
    {
        return $this->belongsTo(User::class, 'action_taken_by');
    }

    public function emergencyType()
    {
        return $this->belongsTo(EmergencyType::class, 'emergency_type_id');
    }

    public function updates()
    {
        return $this->hasMany(EmergencyUpdate::class, 'notice_id')->orderBy('created_at', 'desc');
    }
}