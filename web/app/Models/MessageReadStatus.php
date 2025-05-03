<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageReadStatus extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     */
    protected $table = 'message_read_status';
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'read_status_id';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'message_id',
        'reader_id',
        'reader_type',
        'read_at'
    ];
    
    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];
    
    /**
     * Get the message that this read status belongs to.
     */
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
    
    /**
     * Get the reader entity (User, Beneficiary, or FamilyMember).
     */
    public function reader()
    {
        if ($this->reader_type === 'cose_staff') {
            return $this->belongsTo(User::class, 'reader_id');
        } elseif ($this->reader_type === 'beneficiary') {
            return $this->belongsTo(Beneficiary::class, 'reader_id', 'beneficiary_id');
        } elseif ($this->reader_type === 'family_member') {
            return $this->belongsTo(FamilyMember::class, 'reader_id', 'family_member_id');
        }
        
        return null;
    }
}