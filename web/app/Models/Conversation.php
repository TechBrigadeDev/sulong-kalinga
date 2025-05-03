<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';
    protected $primaryKey = 'conversation_id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'is_group_chat',
        'last_message_id',
    ];

    /**
     * Get the conversation's last message.
     */
    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id', 'message_id');
    }

    /**
     * Get all messages in the conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id', 'conversation_id')
                    ->orderBy('message_timestamp', 'asc');
    }

    /**
     * Get all participants in the conversation.
     */
    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class, 'conversation_id', 'conversation_id');
    }

    /**
     * Check if a user is a participant in this conversation.
     */
    public function hasParticipant($participantId, $participantType)
    {
        return $this->participants()
                    ->where('participant_id', $participantId)
                    ->where('participant_type', $participantType)
                    ->whereNull('left_at')
                    ->exists();
    }

    /**
     * Customize the JSON serialization to prevent circular references
     */
    protected $hidden = ['participants.conversation', 'messages.conversation'];
}