<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class FamilyMember extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'family_members';
    protected $primaryKey = 'family_member_id'; // Explicitly set the primary key

    protected $fillable = [
        'first_name', 'last_name', 'birthday', 'mobile', 'landline', 'email', 'password',
        'street_address', 'barangay_id', 'gender', 'related_beneficiary_id', 'relation_to_beneficiary',
        'is_primary_caregiver', 'created_by', 'updated_by'
    ];

    protected $guard = 'family';
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Define the relationship to the Beneficiary model
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'related_beneficiary_id');
    }

    public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }
}