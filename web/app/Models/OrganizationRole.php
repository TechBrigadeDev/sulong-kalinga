<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationRole extends Model
{
    use HasFactory;

    protected $primaryKey = 'organization_role_id';

    protected $fillable = [
        'role_name', 'area'
    ];

    // Define the relationship with the User model
    public function users()
    {
        return $this->hasMany(User::class, 'organization_role_id');
    }
}