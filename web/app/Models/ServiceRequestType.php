<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestType extends Model
{
    use HasFactory;

    protected $primaryKey = 'service_type_id';
    
    protected $fillable = [
        'name',
        'color_code',
        'description'
    ];

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'service_type_id');
    }
}