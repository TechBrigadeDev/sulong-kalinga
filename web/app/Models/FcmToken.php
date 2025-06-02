<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_device_id',
        'token',
        'is_active',
        'last_used_at',
    ];

    public function mobileDevice()
    {
        return $this->belongsTo(MobileDevice::class);
    }
}
