<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';
    protected $primaryKey = 'message_id';
    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'content',
        'is_unsent',
        'message_timestamp',
    ];

    protected $casts = [
        'message_timestamp' => 'datetime',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'conversation_id');
    }

    /**
     * Modified sender relationship - this is likely where the issue is
     */
    public function sender()
    {
        if ($this->sender_type === 'cose_staff') {
            return $this->morphTo(User::class, 'sender_id');
        } elseif ($this->sender_type === 'beneficiary') {
            return $this->morphTo(Beneficiary::class, 'sender_id', 'beneficiary_id');
        } elseif ($this->sender_type === 'family_member') {
            return $this->morphTo(FamilyMember::class, 'sender_id', 'family_member_id');
        }
        
        return null;
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id', 'message_id');
    }

    /**
     * Get read statuses for the message.
     */
    public function readStatuses()
    {
        return $this->hasMany(MessageReadStatus::class, 'message_id', 'message_id');
    }

    /**
     * Check if the message has been read by a specific user.
     */
    public function isReadBy($readerId, $readerType)
    {
        return $this->readStatuses()
                    ->where('reader_id', $readerId)
                    ->where('reader_type', $readerType)
                    ->exists();
    }

    /**
     * Mark the message as read by a user.
     */
    public function markAsReadBy($readerId, $readerType)
    {
        // Check if already read
        if ($this->isReadBy($readerId, $readerType)) {
            return;
        }

        // Mark as read
        MessageReadStatus::create([
            'message_id' => $this->message_id,
            'reader_id' => $readerId,
            'reader_type' => $readerType,
            'read_at' => now(),
        ]);
    }

    /**
     * Customize the JSON serialization 
     */
    protected $hidden = ['conversation.messages'];
}