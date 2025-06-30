<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileDevice extends Model
{
    use HasFactory;

    protected $table = 'mobile_devices';

    protected $fillable = [
        'user_id',
        'user_type',
        'device_uuid',
        'device_type',
        'device_model',
        'os_version',
    ];

    /**
     * Polymorphic user relationship (if you use multiple user types)
     */
    public function user()
    {
        // If you want to support multiple user types (beneficiary, family_member, cose_staff)
        return $this->morphTo(null, 'user_type', 'user_id');
    }

    /**
     * FCM tokens associated with this device
     */
    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class, 'device_uuid', 'device_uuid');
    }
}
