<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    
    const TOKEN_EXPIRY_HOURS = 24;
    
    protected $table = 'password_resets';
    public $timestamps = false;
    
    protected $fillable = [
        'email',
        'token',
        'user_type',
        'created_at',
        'expires_at',
        'used_at'
    ];
    
    public static function createToken($email, $userType)
    {
        // Generate a random token
        $token = \Illuminate\Support\Str::random(64);
        
        // Create or update the reset record
        self::updateOrCreate(
            ['email' => $email, 'user_type' => $userType],
            [
                'token' => $token, 
                'created_at' => now(),
                'expires_at' => now()->addHours(self::TOKEN_EXPIRY_HOURS),
                'used_at' => null
            ]
        );
        
        return $token;
    }
    
    public static function validateToken($token, $email)
    {
        $reset = self::where('token', $token)
                     ->where('email', $email)
                     ->where('expires_at', '>', now())
                     ->whereNull('used_at')
                     ->first();
                     
        return $reset ? true : false;
    }
    
    public static function markAsUsed($token, $email)
    {
        return self::where('token', $token)
                   ->where('email', $email)
                   ->update(['used_at' => now()]);
    }
}