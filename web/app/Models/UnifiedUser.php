<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\Beneficiary;
use Illuminate\Foundation\Auth\CoseUser;
use Illuminate\Foundation\Auth\FamilyMember;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnifiedUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'email', 'username', 'password', 'first_name', 'last_name', 'mobile', 'role_id', 'status',
        'user_type', 'cose_user_id', 'beneficiary_id', 'family_member_id'
    ];

    protected $hidden = [
        'password',
    ];

    // Relationships to original tables
    public function coseDetails()
    {
        return $this->belongsTo(CoseUser::class, 'cose_user_id');
    }

    public function beneficiaryDetails()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function familyMemberDetails()
    {
        return $this->belongsTo(FamilyMember::class, 'family_member_id');
    }
}
