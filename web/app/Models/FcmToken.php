<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the FCM token based on role
     */
    public function user()
    {
        switch ($this->role) {
            case 'beneficiary':
                return $this->belongsTo(\App\Models\Beneficiary::class, 'user_id', 'beneficiary_id');
            case 'family_member':
                return $this->belongsTo(\App\Models\FamilyMember::class, 'user_id', 'family_member_id');
            case 'cose_staff':
                return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
            default:
                return null;
        }
    }

    /**
     * Scope to get tokens by user and role
     */
    public function scopeByUser($query, $userId, $role)
    {
        return $query->where('user_id', $userId)->where('role', $role);
    }

    /**
     * Register or update FCM token for a user (replaces existing tokens)
     */
    public static function registerToken($userId, $role, $token)
    {
        // Delete existing tokens for this user and role to ensure only one active token
        static::where('user_id', $userId)->where('role', $role)->delete();
        
        return static::create([
            'user_id' => $userId,
            'role' => $role,
            'token' => $token,
        ]);
    }

    /**
     * Get FCM token by user ID and role
     */
    public static function getTokenByUser($userId, $role)
    {
        return static::where('user_id', $userId)->where('role', $role)->first();
    }
}
