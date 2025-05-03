<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnifiedUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'email', 'password', 'first_name', 'last_name', 'mobile', 'role_id', 'status',
        'user_type', 'cose_user_id', 'portal_account_id'
    ];

    protected $hidden = [
        'password',
    ];

    // Relationships to original tables
    public function coseDetails()
    {
        return $this->belongsTo(CoseUser::class, 'cose_user_id');
    }

    public function portalDetails()
    {
        return $this->belongsTo(PortalAccount::class, 'portal_account_id');
    }
    public function beneficiaryDetails()
    {
        return $this->hasOne(Beneficiary::class, 'portal_account_id', 'portal_account_id');
    }

    public function familyMemberDetails()
    {
        return $this->hasOne(FamilyMember::class, 'portal_account_id', 'portal_account_id');
    }
}
