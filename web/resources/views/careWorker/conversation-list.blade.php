@foreach($conversations as $convo)
    @php
        // Prepare participant names data for search
        $participantNames = '';
        $groupParticipants = '';
        
        if ($convo->is_group_chat) {
            // For group chats, collect all participant names
            foreach ($convo->participants as $participant) {
                if ($participant->participant_id != Auth::id() || $participant->participant_type != 'cose_staff') {
                    $groupParticipants .= $participant->getParticipantNameAttribute() . ' ';
                }
            }
        } else {
            // For private chats, just the other participant's name
            $participantNames = $convo->other_participant_name ?? '';
        }
    @endphp

    @php
        // Check if conversation has unread messages - FIXED LOGIC
        $hasUnread = false;
        $unreadCount = 0;
        
        // Count all unread messages regardless of last message status
        $unreadCount = \App\Models\Message::where('conversation_id', $convo->conversation_id)
            ->where(function($query) {
                $query->where('sender_id', '!=', Auth::id())
                    ->orWhere('sender_type', '!=', 'cose_staff');
            })
            ->whereDoesntHave('readStatuses', function($query) {
                $query->where('reader_id', Auth::id())
                    ->where('reader_type', 'cose_staff');
            })
            ->count();
        
        // If there are any unread messages, mark the conversation as unread
        $hasUnread = $unreadCount > 0;
    @endphp

    <div class="conversation-item {{ $hasUnread ? 'unread' : '' }}"
        data-conversation-id="{{ $convo->conversation_id }}"
        data-participant-names="{{ $participantNames }}"
        data-group-participants="{{ $groupParticipants }}">
        <div class="d-flex">
            <div class="flex-shrink-0 position-relative">
                @if($convo->is_group_chat)
                    <div class="rounded-circle profile-img-sm d-flex justify-content-center align-items-center bg-primary text-white">
                        <span>{{ $convo->name ? substr($convo->name, 0, 1) : 'G' }}</span>
                    </div>
                @else
                    <img src="{{ asset('images/defaultProfile.png') }}" class="rounded-circle profile-img-sm" alt="User">
                @endif
                
                @if($hasUnread)
                    <span class="unread-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </div>
            <div class="flex-grow-1 ms-3">
            <div class="conversation-title {{ $hasUnread ? 'fw-bold' : '' }}">
                    <div class="name-container">
                        <span class="participant-name">
                            @if($convo->is_group_chat)
                                {{ $convo->name }}
                            @else
                                {{ $convo->other_participant_name ?? 'Unknown User' }}
                            @endif
                        </span>
                        
                        @if(!$convo->is_group_chat)
                            @php
                                $participantType = '';
                                $otherParticipant = null;
                                // Find the other participant's type
                                foreach ($convo->participants as $participant) {
                                    if (!($participant->participant_id == Auth::id() && $participant->participant_type == 'cose_staff')) {
                                        $participantType = $participant->participant_type;
                                        $otherParticipant = $participant;
                                        break;
                                    }
                                }
                                // Convert type to readable name
                                $typeBadgeClass = 'bg-secondary';
                                switch($participantType) {
                                    case 'cose_staff':
                                        // Get the user directly from database instead of relying on other_participant
                                        $staffUser = \App\Models\User::find($otherParticipant->participant_id);
                                        $userRole = $staffUser->role_id ?? 0;
                                        
                                        if ($userRole == 1) {
                                            $participantType = 'Administrator';
                                            $typeBadgeClass = 'bg-danger';
                                        } elseif ($userRole == 2) {
                                            $participantType = 'Care Manager';
                                            $typeBadgeClass = 'bg-primary';
                                        } elseif ($userRole == 3) {
                                            $participantType = 'Care Worker';
                                            $typeBadgeClass = 'bg-info';
                                        } else {
                                            $participantType = 'Staff';
                                        }
                                        break;
                                    case 'beneficiary':
                                        $participantType = 'Beneficiary';
                                        $typeBadgeClass = 'bg-success';
                                        break;
                                    case 'family_member':
                                        $participantType = 'Family Member';
                                        $typeBadgeClass = 'bg-warning text-dark';
                                        break;
                                }
                            @endphp
                            <span class="user-type-badge {{ $typeBadgeClass }}">{{ $participantType }}</span>
                        @endif
                    </div>
                    <small class="conversation-time">
                        @if(isset($convo->lastMessage) && $convo->lastMessage)
                            {{ \Carbon\Carbon::parse($convo->lastMessage->message_timestamp)->diffForHumans(null, true) }}
                        @endif
                    </small>
                </div>
                <p class="conversation-preview {{ $hasUnread ? 'fw-bold' : '' }}">
                    @if(isset($convo->lastMessage))
                        @if($convo->lastMessage->sender_type === 'system')
                            <span class="text-muted fst-italic">{{ $convo->lastMessage->content }}</span>
                        @elseif(isset($convo->lastMessage->is_unsent) && $convo->lastMessage->is_unsent)
                            <span class="text-muted fst-italic">This message was unsent</span>
                        @elseif($convo->lastMessage->sender_id == Auth::id() && $convo->lastMessage->sender_type == 'cose_staff')
                            <span class="text-muted">You: </span>{{ $convo->lastMessage->content }}
                        @else
                            @if($convo->is_group_chat)
                                @php
                                    $senderName = '';
                                    if ($convo->lastMessage->sender_type === 'cose_staff') {
                                        $staff = \App\Models\User::find($convo->lastMessage->sender_id);
                                        $senderName = $staff ? $staff->first_name : 'Unknown';
                                    } elseif ($convo->lastMessage->sender_type === 'beneficiary') {
                                        $beneficiary = \App\Models\Beneficiary::find($convo->lastMessage->sender_id);
                                        $senderName = $beneficiary ? $beneficiary->first_name : 'Unknown';
                                    } elseif ($convo->lastMessage->sender_type === 'family_member') {
                                        $familyMember = \App\Models\FamilyMember::find($convo->lastMessage->sender_id);
                                        $senderName = $familyMember ? $familyMember->first_name : 'Unknown';
                                    }
                                @endphp
                                <span class="text-muted">{{ $senderName }}: </span>
                            @endif
                            {{ $convo->lastMessage->content }}
                        @endif
                    @else
                        <span class="text-muted">No messages yet</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
@endforeach