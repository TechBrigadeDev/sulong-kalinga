<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_uuid',
        'device_type',
        'device_model',
        'os_version',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }
}
