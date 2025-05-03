<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthenticatableBase;
use Illuminate\Notifications\Notifiable;

class PortalAccount extends AuthenticatableBase implements AuthenticatableContract
{
    use HasFactory, Notifiable, Authenticatable;

    protected $table = 'portal_accounts'; // Table name

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'portal_email', 'portal_password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'portal_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // /**
    //  * Get the name of the unique identifier for the user.
    //  *
    //  * @return string
    //  */
    // public function getAuthIdentifierName()
    // {
    //     return 'portal_email';
    // }

    // /**
    //  * Get the unique identifier for the user.
    //  *
    //  * @return mixed
    //  */
    // public function getAuthIdentifier()
    // {
    //     return $this->portal_email;
    // }

    // /**
    //  * Get the password for the user.
    //  *
    //  * @return string
    //  */
    // public function getAuthPassword()
    // {
    //     return $this->portal_password;
    // }

    // /**
    //  * Get the token value for the "remember me" session.
    //  *
    //  * @return string|null
    //  */
    // public function getRememberToken()
    // {
    //     return $this->remember_token;
    // }

    // /**
    //  * Set the token value for the "remember me" session.
    //  *
    //  * @param string|null $value
    //  * @return void
    //  */
    // public function setRememberToken($value)
    // {
    //     $this->remember_token = $value;
    // }

    // /**
    //  * Get the column name for the "remember me" token.
    //  *
    //  * @return string
    //  */
    // public function getRememberTokenName()
    // {
    //     return 'remember_token';
    // }
}