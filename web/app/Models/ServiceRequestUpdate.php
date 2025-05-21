<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestUpdate extends Model
{
    use HasFactory;

    protected $primaryKey = 'update_id';
    
    protected $fillable = [
        'service_request_id',
        'message',
        'update_type',
        'status_change_to',
        'updated_by',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}