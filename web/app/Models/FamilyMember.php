<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $table = 'family_members';
    protected $primaryKey = 'family_member_id'; // Explicitly set the primary key

    protected $fillable = [
        'first_name', 'last_name', 'birthday', 'mobile', 'landline', 'email', 'access',
        'street_address', 'barangay_id', 'gender', 'related_beneficiary_id', 'relation_to_beneficiary',
        'is_primary_caregiver', 'portal_account_id', 'created_by', 'updated_by'
    ];

    // Define the relationship to the Beneficiary model
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'related_beneficiary_id');
    }
    
    public function portalAccount()
    {
        return $this->belongsTo(PortalAccount::class, 'portal_account_id');
    }

    public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }
}