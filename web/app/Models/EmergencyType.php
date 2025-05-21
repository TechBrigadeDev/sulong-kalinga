<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyType extends Model
{
    use HasFactory;

    protected $primaryKey = 'emergency_type_id';
    
    protected $fillable = [
        'name',
        'color_code',
        'description'
    ];

    public function emergencyNotices()
    {
        return $this->hasMany(EmergencyNotice::class, 'emergency_type_id');
    }
}