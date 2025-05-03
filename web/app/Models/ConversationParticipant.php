<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;

class ConversationParticipant extends Model
{
    protected $table = 'conversation_participants';
    protected $primaryKey = 'conversation_participant_id';
    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'participant_id',
        'participant_type',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'conversation_id');
    }

    /**
     * Get the participant model (polymorphic).
     */
    public function participant()
    {
        // Fix the polymorphic relationship by properly handling each type
        switch($this->participant_type) {
            case 'cose_staff':
                return $this->belongsTo(User::class, 'participant_id');
            case 'beneficiary':
                return $this->belongsTo(Beneficiary::class, 'participant_id');
            case 'family_member':
                return $this->belongsTo(FamilyMember::class, 'participant_id');
            default:
                return null;
        }
    }

    /**
     * Get the name of the participant for display purposes.
     */
    public function getParticipantNameAttribute()
    {
        switch($this->participant_type) {
            case 'cose_staff':
                $user = User::find($this->participant_id);
                return $user ? $user->first_name . ' ' . $user->last_name : 'Unknown Staff';
            case 'beneficiary':
                $beneficiary = Beneficiary::find($this->participant_id);
                return $beneficiary ? $beneficiary->first_name . ' ' . $beneficiary->last_name : 'Unknown Beneficiary';
            case 'family_member':
                $familyMember = FamilyMember::find($this->participant_id);
                return $familyMember ? $familyMember->first_name . ' ' . $familyMember->last_name : 'Unknown Family Member';
            default:
                return 'Unknown User';
        }
    }
}