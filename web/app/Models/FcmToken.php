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
        'mobile_device_id',
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
        return $this->belongsTo(MobileDevice::class, 'mobile_device_id');
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
     * @param int $userId
     * @param string $role
     * @param string $token
     * @param int $mobileDeviceId
     * @return static
     */
    public static function registerToken($userId, $role, $token, $mobileDeviceId)
    {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'role' => $role,
                'mobile_device_id' => $mobileDeviceId,
            ],
            [
                'token' => $token,
            ]
        );
    }

    /**
     * Get FCM token by user ID, role, and device
     * @param int $userId
     * @param string $role
     * @param int $mobileDeviceId
     * @return static|null
     */
    public static function getTokenByUserAndDevice($userId, $role, $mobileDeviceId)
    {
        return static::where('user_id', $userId)
            ->where('role', $role)
            ->where('mobile_device_id', $mobileDeviceId)
            ->first();
    }
}
