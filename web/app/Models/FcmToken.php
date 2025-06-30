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
        'device_uuid',
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
     * Device associated with this token
     */
    public function device()
    {
        return $this->belongsTo(MobileDevice::class, 'device_uuid', 'device_uuid');
    }

    /**
     * Scope to get tokens by user and role
     */
    public function scopeByUser($query, $userId, $role)
    {
        return $query->where('user_id', $userId)->where('role', $role);
    }

    /**
     * Register or update FCM token for a user and device (device-specific)
     */
    public static function registerToken($userId, $role, $token, $deviceUuid)
    {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'role' => $role,
                'device_uuid' => $deviceUuid,
            ],
            [
                'token' => $token,
            ]
        );
    }

    /**
     * Get FCM token by user ID, role, and device
     */
    public static function getTokenByUserAndDevice($userId, $role, $deviceUuid)
    {
        return static::where('user_id', $userId)
            ->where('role', $role)
            ->where('device_uuid', $deviceUuid)
            ->first();
    }
}
