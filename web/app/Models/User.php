<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // use HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable;

     //FOR LOGIN WITH USER ROLES
     public function isAdministrator(): bool
     {
         return $this->role_id == 1 && $this->organization_role_id != 1;
     }
 
     public function isExecutiveDirector(): bool
     {
         return $this->role_id == 1 && $this->organization_role_id == 1;
     }
 
     public function isCareManager(): bool
     {
         return $this->role_id == 2;
     }
 
     public function isCareWorker(): bool
     {
         return $this->role_id == 3;
     }
 
     public function getRoleName(): string
     {
         if ($this->isExecutiveDirector()) return 'executive_director';
         if ($this->isAdministrator()) return 'administrator';
         if ($this->isCareManager()) return 'care_manager';
         if ($this->isCareWorker()) return 'care_worker';
         return 'unknown';
     }


    protected $table = 'cose_users'; // Updated table name

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name', 'last_name', 'birthday', 'civil_status', 'educational_background',
        'mobile', 'landline', 'personal_email', 'email', 'password', 'address', 'barangay_id',
        'gender', 'religion', 'nationality', 'volunteer_status', 'status_start_date',
        'status_end_date', 'role_id', 'status', 'organization_role_id', 'assigned_municipality_id',
        'assigned_care_manager_id',
        'photo', 'government_issued_id', 'sss_id_number', 'philhealth_id_number',
        'pagibig_id_number', 'cv_resume', 'updated_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'status_start_date' => 'date',
        'status_end_date' => 'date',
    ];

    
    // Get the municipality associated with the beneficiary.
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'assigned_municipality_id', 'municipality_id');
    }

    // Define the relationship to the Barangay model
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }
    
    // Define the relationship with the OrganizationRole model
    public function organizationRole()
    {
        return $this->belongsTo(OrganizationRole::class, 'organization_role_id');
    }

    // Define the relationship with the OrganizationRole model
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'organization_role_id');
    }

     // Define the relationship with the GeneralCarePlan model
     public function generalCarePlans()
     {
         return $this->hasMany(GeneralCarePlan::class, 'care_worker_id');
     }
 
     // Define the relationship with the CareWorkerResponsibility model
     public function careWorkerResponsibilities()
     {
         return $this->hasManyThrough(GeneralCarePlan::class, CareWorkerResponsibility::class, 'care_worker_id', 'id', 'id', 'general_care_plan_id');
     }

     public function assignedBeneficiaries()
    {
        return $this->hasManyThrough(
            Beneficiary::class,          // The model we want to access (Beneficiary)
            GeneralCarePlan::class,      // The intermediate model (GeneralCarePlan)
            'care_worker_id',            // Foreign key on GeneralCarePlan
            'general_care_plan_id',      // Foreign key on Beneficiary
            'general_care_plan_id',                        // Local key on User
            'general_care_plan_id'                         // Local key on GeneralCarePlan
        );
    }

    /**
     * Get the care manager assigned to this care worker
     */
    public function assignedCareManager()
    {
        return $this->belongsTo(User::class, 'assigned_care_manager_id', 'id');
    }

    /**
     * Get the care workers assigned to this care manager
     */
    public function assignedCareWorkers()
    {
        return $this->hasMany(User::class, 'assigned_care_manager_id', 'id');
    }

    public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }
    
}