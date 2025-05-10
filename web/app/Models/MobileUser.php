<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MobileUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users_consolidated';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'mobile',
        'role_id',
        'status',
        'user_type',
        'cose_user_id',
        'portal_account_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the COSE user associated with this user, if applicable.
     */
    public function coseUser()
    {
        return $this->belongsTo(User::class, 'cose_user_id', 'id');
    }

    /**
     * Get the portal account associated with this user, if applicable.
     */
    public function portalAccount()
    {
        return $this->belongsTo(PortalAccount::class, 'portal_account_id', 'id');
    }

    /**
     * Get the beneficiary associated with this user, if applicable.
     */
    public function beneficiary()
    {
        if ($this->role_id === 4 && $this->portal_account_id) {
            return Beneficiary::where('portal_account_id', $this->portal_account_id)->first();
        }
        return null;
    }

    /**
     * Get the family member associated with this user, if applicable.
     */
    public function familyMember()
    {
        if ($this->role_id === 5 && $this->portal_account_id) {
            return FamilyMember::where('portal_account_id', $this->portal_account_id)->first();
        }
        return null;
    }

    /**
     * Check if this user is a COSE staff member.
     */
    public function isCoseStaff(): bool
    {
        return $this->user_type === 'cose';
    }

    /**
     * Check if this user is a portal user (beneficiary or family member).
     */
    public function isPortalUser(): bool
    {
        return $this->user_type === 'portal';
    }
}