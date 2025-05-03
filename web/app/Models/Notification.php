<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Models\Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';
    
    protected $fillable = [
        'user_id',
        'user_type',
        'message_title',
        'message',
        'date_created',
        'is_read'
    ];
    
    protected $casts = [
        'date_created' => 'datetime',
        'is_read' => 'boolean',
    ];
    
    /**
     * Get the owner of the notification (polymorphic)
     */
    public function notifiable()
    {
        if ($this->user_type === 'beneficiary') {
            return $this->belongsTo(Beneficiary::class, 'user_id', 'beneficiary_id');
        } elseif ($this->user_type === 'family_member') {
            return $this->belongsTo(FamilyMember::class, 'user_id', 'family_member_id');
        } elseif ($this->user_type === 'cose_staff') {
            return $this->belongsTo(User::class, 'user_id', 'id');
        }
        
        return null;
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
        
        return $this;
    }
    
    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->is_read = false;
        $this->save();
        
        return $this;
    }
    
    /**
     * Scope query to only include unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function getCreatedAtAttribute()
    {
        return $this->date_created;
    }
}