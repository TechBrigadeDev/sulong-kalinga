@php
function getFileIconClass($fileType) {
    // Check by MIME type
    $fileType = strtolower($fileType);
    
    if (str_contains($fileType, 'pdf')) {
        return 'bi-file-earmark-pdf';
    } elseif (str_contains($fileType, 'word') || str_contains($fileType, 'doc')) {
        return 'bi-file-earmark-word';
    } elseif (str_contains($fileType, 'excel') || str_contains($fileType, 'spreadsheet')) {
        return 'bi-file-earmark-excel';
    } elseif (str_contains($fileType, 'text')) {
        return 'bi-file-earmark-text';
    } elseif (str_contains($fileType, 'image')) {
        return 'bi-file-earmark-image';
    } else {
        return 'bi-file-earmark';
    }
}
@endphp

<!-- Conversation Header -->
<div class="conversation-title-area">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            @if($conversation->is_group_chat)
                <div class="rounded-circle profile-img-sm d-flex justify-content-center align-items-center bg-primary text-white me-2">
                    <span>{{ $conversation->name ? substr($conversation->name, 0, 1) : 'G' }}</span>
                </div>
                <h5 class="mb-0">{{ $conversation->name }}</h5>
            @else
                <img src="{{ $conversation->other_participant_photo_url ?? asset('images/defaultProfile.png') }}" class="rounded-circle profile-img-sm me-2" alt="User">
                <h5 class="mb-0">{{ $conversation->other_participant_name ?? 'Unknown User' }}</h5>
            @endif
        </div>
        
        <div class="d-flex align-items-center">
            <!-- Search icon - ADD THIS -->
            <button class="btn btn-sm btn-outline-secondary me-2" id="messageSearchBtn">
                <i class="bi bi-search"></i>
            </button>
            
            <!-- Group actions dropdown -->
            @if($conversation->is_group_chat)
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="groupActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="groupActionsDropdown">
                        <li><a class="dropdown-item view-members-btn" href="#" data-conversation-id="{{ $conversation->conversation_id }}">
                            <i class="bi bi-people-fill text-primary me-2"></i> View Members
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item leave-group-btn" href="#" data-conversation-id="{{ $conversation->conversation_id }}">
                            <i class="bi bi-box-arrow-right text-danger me-2"></i> Leave Group
                        </a></li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Search container -->
    <div class="message-search-container" id="messageSearchContainer" style="display: none;">
        <div class="input-group">
            <input type="text" class="form-control" id="messageSearchInput" placeholder="Search in conversation...">
            <button class="btn btn-outline-secondary search-nav-btn" id="searchPrevBtn" disabled>
                <i class="bi bi-arrow-up"></i>
            </button>
            <button class="btn btn-outline-secondary search-nav-btn" id="searchNextBtn" disabled>
                <i class="bi bi-arrow-down"></i>
            </button>
            <button class="btn btn-outline-secondary" id="closeSearchBtn">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="search-results-count" id="searchResultsCount"></div>
    </div>

</div>

<!-- Messages Container -->
<div class="messages-container" id="messagesContainer">
    <!-- Conversation Creation Info -->
    <div class="text-center my-3 conversation-created-info">
        <span class="badge bg-light text-secondary">
            @if($conversation->created_at)
                Conversation started on {{ \Carbon\Carbon::parse($conversation->created_at)->format('F j, Y') }}
            @endif
        </span>
    </div>

    <!-- Group Messages by Date -->
    @php
        $currentDay = null;
        $dates = [];
        
        // First collect all unique dates
        foreach($messages as $msg) {
            $dates[\Carbon\Carbon::parse($msg->message_timestamp)->format('Y-m-d')] = true;
        }
        $dates = array_keys($dates);
    @endphp

    @foreach($dates as $date)
        <!-- Date Separator - centered and consistent -->
        <div class="text-center my-3">
            <span class="badge bg-secondary date-separator">
                {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
            </span>
        </div>
        
        <!-- Messages for this date -->
        @foreach($messages->filter(function($msg) use ($date) {
            return \Carbon\Carbon::parse($msg->message_timestamp)->format('Y-m-d') === $date;
        }) as $message)
            @if($message->sender_type === 'system')
                <!-- System message, unchanged -->
                <div class="message system">
                    <div class="message-content {{ strpos($message->content, 'left the group') !== false ? 'leave-message' : (strpos($message->content, 'joined the group') !== false ? 'join-message' : '') }}">
                        {{ $message->content }}
                    </div>
                    <div class="message-time">
                        <small>{{ \Carbon\Carbon::parse($message->message_timestamp)->format('g:i A') }}</small>
                    </div>
                </div>
            @else
                @php
                    // CRITICAL FIX: Better check for current user, considering both 'family' and 'family_member' types
                    $isCurrentUserSender = $message->sender_id == Auth::guard($rolePrefix)->id() && 
                        ($message->sender_type == $rolePrefix || 
                        ($rolePrefix == 'family' && $message->sender_type == 'family_member'));
                @endphp
                <div class="message {{ $isCurrentUserSender ? 'outgoing' : 'incoming' }}" data-message-id="{{ $message->message_id }}">
                    @if(!$isCurrentUserSender)
                        <!-- INCOMING MESSAGE - with profile image -->
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img src="{{ $message->sender_photo_url ?? asset('images/defaultProfile.png') }}" class="rounded-circle" width="30" height="30" alt="User">
                            </div>
                            <div class="flex-grow-1 ms-2">
                                @if($conversation->is_group_chat)
                                    <div class="message-sender">
                                        <small class="text-muted fw-bold">
                                            {{ $message->sender_name ?? 'Unknown' }}
                                            @php
                                                // Set the sender role correctly based on sender type
                                                $senderRole = "Unknown";
                                                if ($message->sender_type === 'cose_staff') {
                                                    // Try to get more specific staff role
                                                    try {
                                                        $staffUser = \App\Models\User::find($message->sender_id);
                                                        $roleId = $staffUser ? $staffUser->role_id : 0;
                                                        
                                                        if ($roleId == 1) {
                                                            $senderRole = "Administrator";
                                                        } elseif ($roleId == 2) {
                                                            $senderRole = "Care Manager";
                                                        } elseif ($roleId == 3) {
                                                            $senderRole = "Care Worker";
                                                        } else {
                                                            $senderRole = "Staff";
                                                        }
                                                    } catch (\Exception $e) {
                                                        $senderRole = "Staff";
                                                    }
                                                } elseif ($message->sender_type === 'beneficiary') {
                                                    $senderRole = "Beneficiary";
                                                } elseif ($message->sender_type === 'family_member' || $message->sender_type === 'family') {
                                                    $senderRole = "Family Member";
                                                }
                                            @endphp
                                            <span class="sender-role">({{ $senderRole }})</span>
                                        </small>
                                    </div>
                                @endif
                                
                                <!-- Message content for incoming messages -->
                                @if(isset($message->is_unsent) && $message->is_unsent)
                                    <div class="message-content unsent">
                                        <em>This message was unsent</em>
                                    </div>
                                @else
                                    @if($message->content)
                                        <div class="message-content">
                                            {{ $message->content }}
                                        </div>
                                    @endif
                                    
                                    <!-- Attachments -->
                                    <!-- Message attachments - FIXED -->
                                    @if($message->attachments && $message->attachments->count() > 0)
                                        <div class="message-attachments">
                                            @foreach($message->attachments as $attachment)
                                                <div class="attachment-container">
                                                    @php
                                                        // Ensure path doesn't have public/ prefix for storage URLs
                                                        $filePath = str_replace('public/', '', $attachment->file_path);
                                                        
                                                        // Determine if this is an image
                                                        $isImage = false;
                                                        if (isset($attachment->is_image)) {
                                                            $isImage = $attachment->is_image === true || 
                                                                    $attachment->is_image === 1 || 
                                                                    $attachment->is_image === '1';
                                                        } else {
                                                            // Check by file extension
                                                            $fileExtension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                                                            $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        }
                                                    @endphp
                                                    
                                                    <a href="{{ $attachment->file_url }}" 
                                                    target="_blank" 
                                                    class="{{ $isImage ? 'attachment-link' : 'attachment-file' }}">
                                                        @if($isImage)
                                                            <div class="attachment-loading" id="loading-{{$attachment->attachment_id}}">
                                                                <div class="spinner-border text-primary loading-pulse"></div>
                                                            </div>
                                                            <img src="{{ $attachment->file_url }}" 
                                                                class="attachment-img" 
                                                                alt="{{ $attachment->file_name }}"
                                                                onload="document.getElementById('loading-{{$attachment->attachment_id}}').style.display='none';"
                                                                onerror="this.onerror=null; this.src='{{ asset('images/file-icon.png') }}';">
                                                        @else
                                                            <div class="file-icon">
                                                                <i class="bi {{ getFileIconClass($attachment->file_type) }}"></i>
                                                            </div>
                                                        @endif
                                                    </a>
                                                    
                                                    <div class="attachment-filename">
                                                        {{ \Illuminate\Support\Str::limit($attachment->file_name, 15) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <!-- Message Time -->
                                    <div class="message-time">
                                        <small>{{ \Carbon\Carbon::parse($message->message_timestamp)->format('g:i A') }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- OUTGOING MESSAGE - no profile image -->
                        <div class="outgoing-message-wrapper">
                            <!-- Message content for outgoing messages -->
                            @if(isset($message->is_unsent) && $message->is_unsent)
                                <div class="message-content unsent">
                                    <em>This message was unsent</em>
                                </div>
                            @else
                                @if($message->content)
                                    <div class="message-content">
                                        {{ $message->content }}
                                    </div>
                                @endif
                                
                                <!-- Attachments -->
                                <!-- Message attachments - FIXED -->
                                @if($message->attachments && $message->attachments->count() > 0)
                                    <div class="message-attachments">
                                        @foreach($message->attachments as $attachment)
                                            <div class="attachment-container">
                                                @php
                                                    // Ensure path doesn't have public/ prefix for storage URLs
                                                    $filePath = str_replace('public/', '', $attachment->file_path);
                                                    
                                                    // Determine if this is an image
                                                    $isImage = false;
                                                    if (isset($attachment->is_image)) {
                                                        $isImage = $attachment->is_image === true || 
                                                                $attachment->is_image === 1 || 
                                                                $attachment->is_image === '1';
                                                    } else {
                                                        // Check by file extension
                                                        $fileExtension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    }
                                                @endphp
                                                
                                                <a href="{{ $attachment->file_url }}" 
                                                target="_blank" 
                                                class="{{ $isImage ? 'attachment-link' : 'attachment-file' }}">
                                                    @if($isImage)
                                                        <div class="attachment-loading" id="loading-{{$attachment->attachment_id}}">
                                                            <div class="spinner-border text-primary loading-pulse"></div>
                                                        </div>
                                                        <img src="{{ $attachment->file_url }}" 
                                                            class="attachment-img" 
                                                            alt="{{ $attachment->file_name }}"
                                                            onload="document.getElementById('loading-{{$attachment->attachment_id}}').style.display='none';"
                                                            onerror="this.onerror=null; this.src='{{ asset('images/file-icon.png') }}';">
                                                    @else
                                                        <div class="file-icon">
                                                            <i class="bi {{ getFileIconClass($attachment->file_type) }}"></i>
                                                        </div>
                                                    @endif
                                                </a>
                                                
                                                <div class="attachment-filename">
                                                    {{ \Illuminate\Support\Str::limit($attachment->file_name, 15) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Message Time -->
                                <div class="message-time">
                                    <small>{{ \Carbon\Carbon::parse($message->message_timestamp)->format('g:i A') }}</small>
                                </div>
                                
                                <!-- Message actions dropdown for outgoing messages -->
                                <div class="message-actions">
                                    <div class="dropdown">
                                        <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item unsend-message" href="#" data-message-id="{{$message->message_id}}">
                                                <i class="bi bi-arrow-counterclockwise me-2"></i> Unsend Message
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    @endforeach
</div>

<!-- Message Input Area -->
<div class="message-input-container">
    <form id="messageForm" action="/{{$rolePrefix}}/messaging/send" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="conversation_id" value="{{ $conversation->conversation_id }}">

        <div id="messageErrorContainer" class="alert alert-danger mb-2" style="display: none;"></div>
        <div id="filePreviewContainer" class="file-preview-container mb-2"></div>
        
        <div class="position-relative">
            <textarea class="form-control message-input" id="messageContent" name="content" rows="1" placeholder="Type a message..."></textarea>
            <input type="file" id="fileUpload" name="attachments[]" class="file-upload d-none" multiple accept="image/jpeg,image/png,image/gif,image/webp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain">
            <button type="button" class="attachment-btn" id="attachmentBtn">
                <i class="bi bi-paperclip"></i>
            </button>
            <button type="submit" class="btn btn-primary send-btn" id="sendMessageBtn">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </form>

</div>