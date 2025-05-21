<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyUpdate extends Model
{
    use HasFactory;

    protected $primaryKey = 'update_id';
    
    protected $fillable = [
        'notice_id',
        'message',
        'update_type',
        'status_change_to',
        'updated_by',
    ];

    public function emergencyNotice()
    {
        return $this->belongsTo(EmergencyNotice::class, 'notice_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}