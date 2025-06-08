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
        
        // Get current user details based on portal type
        $currentUserType = $rolePrefix == 'beneficiary' ? 'beneficiary' : 'family';
        $currentUserId = Auth::guard($currentUserType)->id();
        
        // Count all unread messages 
        $unreadCount = \App\Models\Message::where('conversation_id', $convo->conversation_id)
            ->where(function($query) use ($currentUserId, $currentUserType) {
                $query->where('sender_id', '!=', $currentUserId)
                    ->orWhere('sender_type', '!=', $currentUserType);
            })
            ->whereDoesntHave('readStatuses', function($query) use ($currentUserId, $currentUserType) {
                $query->where('reader_id', $currentUserId)
                    ->where('reader_type', $currentUserType);
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
                                // Get current user details based on portal type
                                $currentUserType = $rolePrefix == 'beneficiary' ? 'beneficiary' : 'family';
                                $currentUserId = Auth::guard($currentUserType)->id();
                                
                                // Find the other participant (not the current user)
                                $otherParticipant = null;
                                $participantType = '';
                                $typeBadgeClass = 'bg-secondary';
                                
                                // IMPROVED: Better detection of other participant by explicitly checking BOTH type and ID
                                foreach ($convo->participants as $participant) {
                                    // If this is NOT the current user (check both type AND id)
                                    if (!($participant->participant_type == $currentUserType && 
                                        $participant->participant_id == $currentUserId)) {
                                        $otherParticipant = $participant;
                                        $participantType = $participant->participant_type;
                                        break;
                                    }
                                }
                                
                                // Make sure we found another participant
                                if ($otherParticipant) {
                                    // Determine participant type and badge
                                    switch($participantType) {
                                        case 'cose_staff':
                                            // Use try-catch to handle potential staff lookup errors
                                            try {
                                                $staffUser = \App\Models\User::find($otherParticipant->participant_id);
                                                $userRole = $staffUser ? $staffUser->role_id : 0;
                                                
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
                                                    $typeBadgeClass = 'bg-secondary';
                                                }
                                            } catch (\Exception $e) {
                                                $participantType = 'Staff';
                                                $typeBadgeClass = 'bg-secondary';
                                            }
                                            break;
                                        case 'beneficiary':
                                            $participantType = 'Beneficiary';
                                            $typeBadgeClass = 'bg-success';
                                            break;
                                        case 'family_member':
                                        case 'family':
                                            $participantType = 'Family Member';
                                            $typeBadgeClass = 'bg-warning text-dark';
                                            break;
                                        default:
                                            $participantType = 'Unknown';
                                            $typeBadgeClass = 'bg-secondary';
                                    }
                                } else {
                                    // Fallback if no other participant is found
                                    $participantType = 'Unknown';
                                    $typeBadgeClass = 'bg-secondary';
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
                        <!-- CORRECTED: This is the key fix - check current user correctly -->
                        @elseif($convo->lastMessage->sender_id == $currentUserId && $convo->lastMessage->sender_type == $currentUserType)
                            <span class="text-muted">You: </span>{{ \Illuminate\Support\Str::limit($convo->lastMessage->content, 30) }}
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
                                    } elseif ($convo->lastMessage->sender_type === 'family_member' || $convo->lastMessage->sender_type === 'family') {
                                        $familyMember = \App\Models\FamilyMember::find($convo->lastMessage->sender_id);
                                        $senderName = $familyMember ? $familyMember->first_name : 'Unknown';
                                    }
                                @endphp
                                <span class="text-muted">{{ $senderName }}: </span>
                            @endif
                            {{ \Illuminate\Support\Str::limit($convo->lastMessage->content, 30) }}
                        @endif
                    @else
                        <span class="text-muted">No messages yet</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
@endforeach