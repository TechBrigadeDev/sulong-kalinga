<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\FamilyMember;
use NotificationChannels\Expo\ExpoPushToken;

class Beneficiary extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $table = 'beneficiaries';
    protected $primaryKey = 'beneficiary_id'; // Explicitly set the primary key
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'civil_status', 'gender', 'birthday', 'primary_caregiver',
        'mobile', 'landline', 'street_address', 'barangay_id', 'municipality_id', 'category_id',
        'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_mobile',
        'emergency_contact_email', 'emergency_procedure', 'beneficiary_status_id', 'status_reason', 
        'general_care_plan_id', 'username', 'password', 'beneficiary_signature', 'care_worker_signature', 
        'created_by', 'updated_by', 'photo', 'general_care_plan_doc', 'care_service_agreement_doc',
        'map_location',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guard = 'beneficiary';

    protected $casts = [
        'map_location' => 'array',
    ];

    // Get the category associated with the beneficiary.
    public function category()
    {
        return $this->belongsTo(BeneficiaryCategory::class, 'category_id', 'category_id');
    }

    // Get the status associated with the beneficiary.
    public function status()
    {
        return $this->belongsTo(BeneficiaryStatus::class, 'beneficiary_status_id', 'beneficiary_status_id');
    }

    // Get the municipality associated with the beneficiary.
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'municipality_id');
    }

    // Get the barangay associated with the beneficiary.
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }   
    
    // Get the care plan associated with the beneficiary.
    public function generalCarePlan()
    {
        return $this->hasOne(GeneralCarePlan::class, 'general_care_plan_id');
    }  

    public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class, 'related_beneficiary_id', 'beneficiary_id');
    }
    
    public function assignedCareWorker()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_care_worker_id', 'id');
    }

    public function routeNotificationForExpo($notification = null)
{
    $token = \App\Models\FcmToken::where('user_id', $this->beneficiary_id)
        ->where('role', 'beneficiary')
        ->value('token');
    \Log::info('routeNotificationForExpo called', [
        'beneficiary_id' => $this->beneficiary_id,
        'token' => $token,
    ]);
    // Use the static make() method
    return $token ? [\NotificationChannels\Expo\ExpoPushToken::make($token)] : [];
}

    public function routeNotificationForFcm($notification = null)
    {
        $token = \App\Models\FcmToken::where('user_id', $this->beneficiary_id)
            ->where('role', 'beneficiary')
            ->value('token');
        \Log::info('routeNotificationForFcm called', [
            'beneficiary_id' => $this->beneficiary_id,
            'token' => $token,
        ]);
        return $token ? [$token] : [];
    }
}