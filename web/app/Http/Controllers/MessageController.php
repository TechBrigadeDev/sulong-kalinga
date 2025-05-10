<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\MessageReadStatus;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;

class MessageController extends Controller
{

    /**
     * Display the messaging interface based on user role.
     */
    public function index()
    {
        try {
            Log::info('Messaging index accessed', ['user_id' => Auth::id()]);
            $user = Auth::user();
            $rolePrefix = $this->getRoleRoutePrefix();
            $view = $this->getRoleViewPrefix() . '.messaging';
            
            // Get user's conversations without the problematic relationship
            $conversations = $this->getUserConversations($user);
            
            // Process participant names manually for private conversations
            foreach ($conversations as $conversation) {
                if (!$conversation->is_group_chat) {
                    // For private conversations, get the other participant's name
                    foreach ($conversation->participants as $participant) {
                        if ($participant->participant_id != $user->id || $participant->participant_type != 'cose_staff') {
                            $conversation->other_participant_name = $this->getParticipantName($participant);
                            break;
                        }
                    }
                }
                
                // Check for unread messages
                $conversation->has_unread = $conversation->messages->filter(function($message) {
                    return $message->readStatuses->isEmpty();
                })->isNotEmpty();
            }
            
            // Return the view with the data
            return view($view, compact('conversations', 'rolePrefix'));
        } catch (\Exception $e) {
            Log::error('Error in messaging index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view($this->getRoleViewPrefix() . '.messaging', [
                'conversations' => collect([]),
                'rolePrefix' => $this->getRoleRoutePrefix(),
                'error' => 'Unable to load conversations. Please try again later.'
            ]);
        }

        // Add this to handle the conversation parameter from dropdown
        $selectedConversationId = $request->input('conversation');
        
        return view('admin.messaging', [
            'conversations' => $conversations,
            'rolePrefix' => $this->getRoleRoutePrefix(),
            'selectedConversationId' => $selectedConversationId // Pass this to the view
        ]);
    }
    
    /**
     * View a specific conversation
     */
    public function viewConversation($id)
    {
        try {
            // Get the conversation with modified eager loading (removed the problematic relationship)
            $conversation = Conversation::with([
                'participants',  // Keep this
                'messages.attachments'  // Keep this
            ])->findOrFail($id);
            
            // Add other_participant_name to conversation object for display purposes
            if (!$conversation->is_group_chat) {
                $otherParticipant = $conversation->participants
                    ->where('participant_type', '!=', 'cose_staff')
                    ->where('participant_id', '!=', Auth::id())
                    ->first();
                
                if ($otherParticipant) {
                    // Get participant name using the helper method instead of the relationship
                    $conversation->other_participant_name = $this->getParticipantName($otherParticipant);
                } else {
                    $conversation->other_participant_name = 'Unknown User';
                }
            }
            
            // Get messages for this conversation
            $messages = Message::with(['attachments', 'readStatuses'])
                ->where('conversation_id', $id)
                ->orderBy('message_timestamp', 'asc')
                ->get();
            
            // Add sender name to each message for display
            foreach ($messages as $message) {
                if ($message->sender_type == 'cose_staff') {
                    $sender = User::find($message->sender_id);
                    if ($sender) {
                        $message->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    }
                } elseif ($message->sender_type == 'beneficiary') {
                    $sender = Beneficiary::find($message->sender_id);
                    if ($sender) {
                        $message->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    }
                } elseif ($message->sender_type == 'family_member') {
                    $sender = FamilyMember::find($message->sender_id);
                    if ($sender) {
                        $message->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    }
                }
            }
            
            // Mark all messages as read
            foreach ($messages as $message) {
                if ($message->sender_id != Auth::id() || $message->sender_type != 'cose_staff') {
                    $this->markMessageAsRead($message->message_id, Auth::id(), 'cose_staff');
                }
            }
            
            // Get all conversations for the sidebar using the getUserConversations method
            $conversations = $this->getUserConversations(Auth::user());
            
            // Process participant names manually for private conversations
            foreach ($conversations as $convo) {
                if (!$convo->is_group_chat) {
                    // For private conversations, get the other participant's name
                    foreach ($convo->participants as $participant) {
                        if ($participant->participant_id != Auth::id() || $participant->participant_type != 'cose_staff') {
                            $convo->other_participant_name = $this->getParticipantName($participant);
                            break;
                        }
                    }
                }
            }
            
            // Get role prefix for routes
            $rolePrefix = $this->getRoleRoutePrefix();
            
            // Check if the request is AJAX
            if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'html' => view('admin.conversation-content', compact('conversation', 'messages', 'rolePrefix'))->render()
                ]);
            }
            
            // For normal requests, redirect to the messaging index page with conversation ID parameter
            return redirect()->route($rolePrefix . '.messaging.index', ['conversation' => $id]);
            
        } catch (\Exception $e) {
            Log::error('Error viewing conversation: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading conversation: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route($this->getRoleRoutePrefix() . '.messaging.index')
                ->with('error', 'Unable to view conversation: ' . $e->getMessage());
        }
    }

    /**
     * Mark a message as read by a specific user.
     */
    private function markMessageAsRead($messageId, $userId, $userType)
    {
        try {
            // Check if read status already exists
            $readExists = MessageReadStatus::where('message_id', $messageId)
                ->where('reader_id', $userId)
                ->where('reader_type', $userType)
                ->exists();
                
            if (!$readExists) {
                MessageReadStatus::create([
                    'message_id' => $messageId,
                    'reader_id' => $userId,
                    'reader_type' => $userType,
                    'read_at' => now()
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error marking message as read: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send a message via AJAX or form submission
     */
    public function sendMessage(Request $request)
    {

        Log::info('Request data for file upload', [
            'has_file_attachments' => $request->hasFile('attachments'),
            'all_files' => $request->allFiles(),
            'request_keys' => array_keys($request->all())
        ]);

        try {
            $user = Auth::user();
            $rolePrefix = $this->getRoleRoutePrefix();
            
            $validatedData = $request->validate([
                'conversation_id' => 'required|exists:conversations,conversation_id',
                'content' => 'nullable|string',
                'attachments.*' => 'sometimes|file|max:10240|mimes:jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt', // 10MB max with specific mime types
            ]);
            
            $conversationId = $request->conversation_id;
            $conversation = Conversation::findOrFail($conversationId);
            
            // Check if user is participant
            $isParticipant = $conversation->hasParticipant($user->id, 'cose_staff');
            if (!$isParticipant) {
                return $this->jsonResponse(false, 'You are not a participant in this conversation', 403);
            }
            
            // Create message with all required fields
            $message = new Message([
                'conversation_id' => $conversationId,
                'sender_id' => $user->id,
                'sender_type' => 'cose_staff',
                'content' => $request->content, // Add this to set content
                'message_timestamp' => now(),
            ]);
            $message->save();
            
            // Handle attachments
            if ($request->hasFile('attachments')) {
                Log::info("Request has attachments", [
                    'count' => is_array($request->file('attachments')) ? count($request->file('attachments')) : 1,
                    'conversation_id' => $conversationId
                ]);

                // Handle both array and non-array formats
                $files = $request->file('attachments');
                Log::info("Found files in request:", [
                    'count' => is_array($files) ? count($files) : 1
                ]);

                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    // Check if file is valid
                    if ($file && $file->isValid()) {
                        // Get file info
                        $fileName = $file->getClientOriginalName();
                        $fileType = $file->getMimeType();
                        $fileSize = $file->getSize();
                        
                        // Determine if it's an image
                        $isImage = str_starts_with($fileType, 'image/');
                        
                        // Store file in storage/app/public/attachments folder
                        $filePath = $file->store('attachments', 'public');
                        
                        // Create attachment record
                        $attachment = new MessageAttachment([
                            'message_id' => $message->message_id,
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                            'is_image' => $isImage
                        ]);
                        $attachment->save();
                        
                        // Log successful file upload
                        Log::info("File attachment created", [
                            'message_id' => $message->message_id,
                            'file_name' => $fileName,
                            'file_path' => $filePath
                        ]);
                    } else {
                        Log::error('Invalid file upload', ['filename' => $file->getClientOriginalName()]);
                    }
                }
            } else {
                Log::warning("No attachments found in request", [
                    'has_file' => $request->hasFile('attachments'),
                    'files' => $request->allFiles(),
                ]);
            }
            
            // Update last message in conversation
            $conversation->last_message_id = $message->message_id;
            $conversation->save();

            // After saving the message and attachments
            $attachmentData = [];
            if ($message->attachments->count() > 0) {
                foreach ($message->attachments as $attachment) {
                    $attachmentData[] = [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'file_path' => $attachment->file_path,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'is_image' => $attachment->is_image
                    ];
                }
            }
            
            // If AJAX request, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message_id' => $message->message_id,
                    'message' => [
                        'content' => $message->content,
                    ],
                    'attachments' => $attachmentData
                ]);
            }

            Log::info('Request data for file upload', [
                'has_file_attachments' => $request->hasFile('attachments'),
                'has_array_attachments' => is_array($request->file('attachments')),
                'files_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0,
                'all_files' => $request->allFiles()
            ]);
            
            // Otherwise redirect back
            return redirect()->route($rolePrefix . '.messaging.index', ['conversation' => $conversationId])
                ->with('success', 'Message sent successfully.');
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error sending message: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Message could not be sent: ' . $e->getMessage());
        }
    }

    /**
     * Helper for consistent JSON responses
     */
    private function jsonResponse($success, $message, $statusCode = 200, $data = [])
    {
        return response()->json(array_merge([
            'success' => $success,
            'message' => $message
        ], $data), $statusCode);
    }
    
    /**
     * Create a new private conversation
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createConversation(Request $request)
    {
        try {
            // Log incoming request data for debugging
            Log::info('Create conversation request', [
                'input' => $request->all(),
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role_id,
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'accept_header' => $request->header('Accept')
            ]);

            // Validate request data - accept both parameter formats for compatibility
            $validatedData = $request->validate([
                'recipient_type' => 'required_without:participant_type|in:cose_staff,beneficiary,family_member',
                'recipient_id' => 'required_without:participant_id',
                'participant_type' => 'required_without:recipient_type|in:cose_staff,beneficiary,family_member',
                'participant_id' => 'required_without:recipient_id',
                'initial_message' => 'nullable|string'
            ]);

            // Normalize parameter names for flexibility
            $recipientType = $request->input('recipient_type', $request->input('participant_type'));
            $recipientId = $request->input('recipient_id', $request->input('participant_id'));
            $initialMessage = $request->input('initial_message');

            $user = Auth::user();
            
            // Check if conversation already exists
            $existingConversation = $this->findExistingConversation($user->id, $recipientId, $recipientType);
            if ($existingConversation) {
                // For AJAX requests
                if ($request->ajax() || $request->wantsJson() || $request->header('Accept') == 'application/json') {
                    return response()->json([
                        'success' => true,
                        'exists' => true,
                        'conversation_id' => $existingConversation->conversation_id
                    ]);
                }
                
                // For regular form submissions
                return redirect()->route($this->getRoleRoutePrefix() . '.messaging.index', [
                    'conversation' => $existingConversation->conversation_id
                ])->with('success', 'Conversation already exists');
            }
            
            // Check permission based on user role
            if ($recipientType === 'cose_staff') {
                try {
                    $recipient = User::findOrFail($recipientId);
                    
                    // Apply role-based permission checks
                    if ($user->role_id == 1 && !in_array($recipient->role_id, [1, 2])) { 
                        // Admin can message other Admins and Care Managers
                        return $this->respondWithError('Administrators can only message other Administrators and Care Managers.', 403, $request);
                    }
                    
                    if ($user->role_id == 2 && !in_array($recipient->role_id, [1, 2, 3])) {
                        // Care Manager can message Admin, other Care Managers, and Care Workers
                        return $this->respondWithError('Care Managers can only message Administrators, other Care Managers, and Care Workers.', 403, $request);
                    }
                    
                    if ($user->role_id == 3 && $recipient->role_id != 2) {
                        // Care Worker can only message Care Managers
                        return $this->respondWithError('Care Workers can only message Care Managers.', 403, $request);
                    }
                } catch (\Exception $e) {
                    Log::error('Error finding recipient user: ' . $e->getMessage());
                    return $this->respondWithError('Could not find the specified recipient.', 404, $request);
                }
            } else if ($recipientType === 'beneficiary' || $recipientType === 'family_member') {
                // Only Care Workers can message beneficiaries and family members
                if ($user->role_id != 3) {
                    return $this->respondWithError('Only Care Workers can message Beneficiaries and Family Members.', 403, $request);
                }
            }
            
            // Create new conversation
            $conversation = new Conversation();
            $conversation->is_group_chat = false;
            $conversation->save();
            
            // Add sender as participant
            $senderParticipant = new ConversationParticipant();
            $senderParticipant->conversation_id = $conversation->conversation_id;
            $senderParticipant->participant_id = $user->id;
            $senderParticipant->participant_type = 'cose_staff';
            $senderParticipant->joined_at = now();
            $senderParticipant->save();
            
            // Add recipient as participant
            $recipientParticipant = new ConversationParticipant();
            $recipientParticipant->conversation_id = $conversation->conversation_id;
            $recipientParticipant->participant_id = $recipientId;
            $recipientParticipant->participant_type = $recipientType;
            $recipientParticipant->joined_at = now();
            $recipientParticipant->save();
            
            // Create initial message if provided
            if (!empty($initialMessage)) {
                $message = new Message();
                $message->conversation_id = $conversation->conversation_id;
                $message->sender_id = $user->id;
                $message->sender_type = 'cose_staff';
                $message->content = $initialMessage;
                $message->message_timestamp = now();
                $message->save();
                
                // Update conversation's last message ID
                $conversation->last_message_id = $message->message_id;
                $conversation->save();
            }
            
            // For AJAX/JSON requests
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') == 'application/json') {
                return response()->json([
                    'success' => true,
                    'conversation_id' => $conversation->conversation_id,
                    'message' => 'Conversation created successfully'
                ]);
            } 
            
            // For regular form submissions
            return redirect()->route($this->getRoleRoutePrefix() . '.messaging.index', [
                'conversation' => $conversation->conversation_id
            ])->with('success', 'Conversation created successfully');
            
        } catch (\Exception $e) {
            Log::error('Error creating conversation: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->respondWithError('An error occurred while creating the conversation: ' . $e->getMessage(), 500, $request);
        }
    }

    /**
     * Helper method to respond with appropriate error format based on request type
     */
    private function respondWithError($message, $status = 400, $request = null)
    {
        if (!$request) $request = request();
        
        // For AJAX/JSON requests
        if ($request->ajax() || $request->wantsJson() || $request->header('Accept') == 'application/json') {
            return response()->json([
                'success' => false,
                'message' => $message
            ], $status);
        }
        
        // For regular form submissions
        return back()->with('error', $message)->withInput();
    }
    
    /**
     * Create a new group conversation with role compatibility validation.
     */
    public function createGroupConversation(Request $request)
    {
        try {
            $user = Auth::user();
            $rolePrefix = $this->getRoleRoutePrefix();
            
            // Validate the request
            $request->validate([
                'name' => 'required|string|max:255',
                'participants' => 'required|array',
                'participants.*.id' => 'required',
                'participants.*.type' => 'required|in:cose_staff,beneficiary,family_member',
                'initial_message' => 'nullable|string',
            ]);
            
            // VALIDATION RULES FOR INCOMPATIBLE ROLES
            if ($user->role_id == 2) { // Care Manager creating group
                $hasAdmin = false;
                $hasCareWorker = false;
                
                // Check for Admins and Care Workers in the participant list
                foreach ($request->participants as $participant) {
                    if ($participant['type'] === 'cose_staff') {
                        $staffUser = User::find($participant['id']);
                        if (!$staffUser) continue;
                        
                        if ($staffUser->role_id == 1) { // Admin
                            $hasAdmin = true;
                        } elseif ($staffUser->role_id == 3) { // Care Worker
                            $hasCareWorker = true;
                        }
                    }
                }
                
                // Cannot have both Admins and Care Workers in the same group
                if ($hasAdmin && $hasCareWorker) {
                    if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot create a group with both Administrators and Care Workers'
                        ], 400);
                    }
                    return back()->with('error', 'Cannot create a group with both Administrators and Care Workers');
                }
            }
            
            // Log the participants for debugging
            Log::info('Creating group conversation with participants:', [
                'participants_count' => count($request->participants),
                'participants' => $request->participants
            ]);

            // Check if participants array is empty after validation
            if (empty($request->participants)) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one participant is required'
                ], 422);
            }
            
            // Create new conversation
            $conversation = Conversation::create([
                'name' => $request->name,
                'is_group_chat' => true,
                'created_by' => $user->id
            ]);
            
            // Add creator as participant
            ConversationParticipant::create([
                'conversation_id' => $conversation->conversation_id,
                'participant_id' => $user->id,
                'participant_type' => 'cose_staff',
                'joined_at' => now(),
            ]);
            
            // Add other participants
            foreach ($request->participants as $participant) {
                // Skip if already added (like the creator)
                if ($participant['id'] == $user->id && $participant['type'] == 'cose_staff') {
                    continue;
                }
                
                ConversationParticipant::create([
                    'conversation_id' => $conversation->conversation_id,
                    'participant_id' => $participant['id'],
                    'participant_type' => $participant['type'],
                    'joined_at' => now(),
                ]);
            }

            // Create a system message for group creation
            $creatorName = $user->first_name . ' ' . $user->last_name;
            $userRole = '';
            if ($user->role_id == 1) {
                $userRole = 'Administrator';
            } elseif ($user->role_id == 2) {
                $userRole = 'Care Manager';
            } elseif ($user->role_id == 3) {
                $userRole = 'Care Worker';
            }

            $message = new Message([
                'conversation_id' => $conversation->conversation_id,
                'sender_id' => 0,
                'sender_type' => 'system',
                'content' => "$creatorName ($userRole) created this group.",
                'message_timestamp' => now(),
            ]);
            $message->save();

            // Update last message in conversation
            $conversation->last_message_id = $message->message_id;
            $conversation->save();

            // Send initial message if provided
            if (!empty($request->initial_message)) {
                $userMessage = new Message([
                    'conversation_id' => $conversation->conversation_id,
                    'sender_id' => $user->id,
                    'sender_type' => 'cose_staff',
                    'content' => $request->initial_message,
                    'message_timestamp' => now(),
                ]);
                $userMessage->save();
                
                // Update last message in conversation
                $conversation->last_message_id = $userMessage->message_id;
                $conversation->save();
            }
            
            
            // For AJAX requests, return JSON
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Group conversation created successfully',
                    'conversation_id' => $conversation->conversation_id
                ]);
            }
            
            return redirect()->route($rolePrefix . '.messaging.conversation', ['conversation' => $conversation->conversation_id])
                ->with('success', 'Group conversation created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating group conversation: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not create group: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Could not create group. Please try again.');
        }
    }
    
    /**
     * Get unread message count for the user.
     */
    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            
            // Get all conversation IDs user is part of
            $conversationIds = ConversationParticipant::where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->pluck('conversation_id');
                
            // Count unread messages - messages sent by others that you haven't read
            $unreadCount = Message::whereIn('conversation_id', $conversationIds)
                ->where(function($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)
                        ->orWhere('sender_type', '!=', 'cose_staff');
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user) {
                    $query->where('reader_id', $user->id)
                        ->where('reader_type', 'cose_staff');
                })
                ->count();
                
            Log::debug('Unread message count', ['count' => $unreadCount, 'user_id' => $user->id]);
                
            return response()->json(['count' => $unreadCount]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json(['count' => 0]); // Fallback to 0 to avoid UI issues
        }
    }
    
    public function getRecentMessages()
    {
        try {
            $user = Auth::user();
            $rolePrefix = $this->getRoleRoutePrefix();
            
            // Get all conversation IDs user is part of
            $conversationIds = ConversationParticipant::where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->pluck('conversation_id');
            
            // Get conversations with all necessary relationships
            $conversations = Conversation::whereIn('conversation_id', $conversationIds)
                ->with([
                    'lastMessage', 
                    'lastMessage.attachments',
                    'participants' => function($query) {
                        $query->whereNull('left_at');
                    }
                ])
                ->get();
            
            // Sort by last message timestamp
            $sortedConversations = $conversations->sortByDesc(function($conversation) {
                return $conversation->lastMessage ? $conversation->lastMessage->message_timestamp : null;
            })->take(10);
            
            $result = [];
            
            foreach ($sortedConversations as $conversation) {
                $lastMessage = $conversation->lastMessage;
                
                // Create data structure for this conversation
                $convoData = [
                    'conversation_id' => $conversation->conversation_id,
                    'is_group_chat' => $conversation->is_group_chat,
                    'name' => $conversation->name ?? '',
                    'has_unread' => false,
                    'unread_count' => 0
                ];
                
                // Set other participant name for private chats
                if (!$conversation->is_group_chat) {
                    $otherParticipant = null;
                    
                    foreach ($conversation->participants as $participant) {
                        if (!($participant->participant_id == $user->id && $participant->participant_type == 'cose_staff')) {
                            $otherParticipant = $participant;
                            break;
                        }
                    }
                    
                    if ($otherParticipant) {
                        $convoData['other_participant_name'] = $this->getParticipantName($otherParticipant);
                    } else {
                        $convoData['other_participant_name'] = 'Unknown User';
                    }
                }
                
                // Add last message data
                if ($lastMessage) {
                    $messageData = [
                        'message_id' => $lastMessage->message_id,
                        'content' => $lastMessage->is_unsent 
                            ? 'This message was unsent' 
                            : $lastMessage->content,
                        'message_timestamp' => $lastMessage->message_timestamp,
                        'sender_name' => 'Unknown'
                    ];
                    
                    // Resolve sender name
                    if ($lastMessage->sender_type == 'cose_staff') {
                        $staff = User::find($lastMessage->sender_id);
                        $messageData['sender_name'] = $staff ? $staff->first_name . ' ' . $staff->last_name : 'Unknown Staff';
                    } else if ($lastMessage->sender_type == 'beneficiary') {
                        $beneficiary = Beneficiary::find($lastMessage->sender_id);
                        $messageData['sender_name'] = $beneficiary ? $beneficiary->first_name . ' ' . $beneficiary->last_name : 'Unknown Beneficiary';
                    } else if ($lastMessage->sender_type == 'family_member') {
                        $familyMember = FamilyMember::find($lastMessage->sender_id);
                        $messageData['sender_name'] = $familyMember ? $familyMember->first_name . ' ' . $familyMember->last_name : 'Unknown Family Member';
                    } else if ($lastMessage->sender_type == 'system') {
                        // Add this case for system messages
                        $messageData['sender_name'] = 'System';
                    }
                    
                    // Add attachment data if message has attachments but no text
                    if ((!$lastMessage->content || trim($lastMessage->content) === '') && 
                        $lastMessage->attachments && $lastMessage->attachments->count() > 0) {
                        
                        // Update this section to check if message is unsent first
                        if ($lastMessage->is_unsent) {
                            $messageData['content'] = 'This message was unsent';
                        } else {
                            if ($lastMessage->attachments->count() == 1) {
                                $attachment = $lastMessage->attachments->first();
                                $messageData['content'] = "📎 " . $attachment->file_name;
                            } else {
                                $messageData['content'] = "📎 " . $lastMessage->attachments->count() . " attachments";
                            }
                        }
                    }
                    
                    $convoData['last_message'] = $messageData;
                    
                    // Check for unread status
                    $hasUnread = false;
                    if ($lastMessage->sender_id != $user->id || $lastMessage->sender_type != 'cose_staff') {
                        $readStatus = MessageReadStatus::where('message_id', $lastMessage->message_id)
                            ->where('reader_id', $user->id)
                            ->where('reader_type', 'cose_staff')
                            ->exists();
                        $hasUnread = !$readStatus;
                    }
                    
                    $convoData['has_unread'] = $hasUnread;
                }
                
                $result[] = $convoData;
            }
            
            return response()->json([
                'conversations' => $result,
                'route_prefix' => $rolePrefix
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting recent messages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'conversations' => [],
                'route_prefix' => $rolePrefix,
                'error' => 'Failed to load messages'
            ]);
        }
    }

    // Add this helper method to get participant names
    private function getParticipantName($participant)
    {
        try {
            switch ($participant->participant_type) {
                case 'cose_staff':
                    $user = \App\Models\User::find($participant->participant_id);
                    return $user ? $user->first_name . ' ' . $user->last_name : 'Unknown Staff';
                
                case 'beneficiary':
                    $beneficiary = \App\Models\Beneficiary::find($participant->participant_id);
                    return $beneficiary ? $beneficiary->first_name . ' ' . $beneficiary->last_name : 'Unknown Beneficiary';
                    
                case 'family_member':
                    $familyMember = \App\Models\FamilyMember::find($participant->participant_id);
                    return $familyMember ? $familyMember->first_name . ' ' . $familyMember->last_name : 'Unknown Family Member';
                    
                default:
                    return 'Unknown User';
            }
        } catch (\Exception $e) {
            \Log::error('Error getting participant name: ' . $e->getMessage());
            return 'Unknown User';
        }
    }
    
    /**
     * Get user's conversations.
     */
    private function getUserConversations($user)
    {
        $conversations = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at');
        })
        ->with([
            'lastMessage', 
            'lastMessage.attachments',
            'participants' => function($query) {
                $query->whereNull('left_at');
            }
        ])
        ->orderBy('updated_at', 'desc')
        ->get();

        // Process conversations to add other participant name for private chats
        foreach ($conversations as $conversation) {
            if (!$conversation->is_group_chat) {
                // For private conversations, find the other participant
                $otherParticipant = null;
                
                foreach ($conversation->participants as $participant) {
                    if (!($participant->participant_id == $user->id && 
                        $participant->participant_type == 'cose_staff')) {
                        $otherParticipant = $participant;
                        break;
                    }
                }
                
                if ($otherParticipant) {
                    // Use the helper method to get the name
                    $conversation->other_participant_name = $this->getParticipantName($otherParticipant);
                    $conversation->other_participant_type = $otherParticipant->participant_type;
                } else {
                    $conversation->other_participant_name = 'No Other Participant';
                }
            }
        }

        return $conversations;
    }
    
    /**
     * Find an existing conversation between two participants
     */
    private function findExistingConversation($userId, $recipientId, $recipientType)
    {
        // Get all conversations where the current user is a participant
        $userConversations = ConversationParticipant::where('participant_id', $userId)
            ->where('participant_type', 'cose_staff')
            ->whereNull('left_at')
            ->pluck('conversation_id');
        
        // Find conversations with both participants and not group chats
        return Conversation::whereIn('conversation_id', function($query) use ($userConversations, $recipientId, $recipientType) {
                $query->select('conversation_id')
                    ->from('conversation_participants')
                    ->whereIn('conversation_id', $userConversations)
                    ->where('participant_id', $recipientId)
                    ->where('participant_type', $recipientType)
                    ->whereNull('left_at');
            })
            ->where('is_group_chat', false)
            ->first();
    }
    
    /**
     * Mark all messages in a conversation as read
     */
    public function markConversationAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            $conversationId = $request->input('conversation_id');
            
            // Get unread messages in this conversation not sent by the current user
            $messages = Message::where('conversation_id', $conversationId)
                ->where(function($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)
                        ->orWhere('sender_type', '!=', 'cose_staff');
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user) {
                    $query->where('reader_id', $user->id)
                        ->where('reader_type', 'cose_staff');
                })
                ->get();
            
            // Mark each message as read
            foreach ($messages as $message) {
                MessageReadStatus::firstOrCreate([
                    'message_id' => $message->message_id,
                    'reader_id' => $user->id,
                    'reader_type' => 'cose_staff',
                    'read_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'count' => count($messages)
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking conversation as read: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error marking messages as read'
            ]);
        }
    }
    
    /**
     * Get the route prefix based on user role.
     */
    private function getRoleRoutePrefix()
    {
        $user = Auth::user();
        if ($user->role_id == 1) {
            return 'admin';
        } elseif ($user->role_id == 2) {
            return 'care-manager';
        } elseif ($user->role_id == 3) {
            return 'care-worker';
        }
        return 'admin'; // Default fallback
    }
    
    /**
     * Get the view prefix based on user role.
     */
    private function getRoleViewPrefix()
    {
        $user = Auth::user();
        if ($user->role_id == 1) {
            return 'admin';
        } elseif ($user->role_id == 2) {
            return 'careManager';
        } elseif ($user->role_id == 3) {
            return 'careWorker';
        }
        return 'admin'; // Default fallback
    }

    /**
     * Mark all unread messages for the current user as read.
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            
            // Get all conversation IDs user is part of
            $conversationIds = ConversationParticipant::where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->pluck('conversation_id');
                
            // Get all unread messages from these conversations
            $unreadMessages = Message::whereIn('conversation_id', $conversationIds)
                ->where(function($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id)
                        ->orWhere('sender_type', '!=', 'cose_staff');
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user) {
                    $query->where('reader_id', $user->id)
                        ->where('reader_type', 'cose_staff');
                })
                ->get();
                
            // Mark each message as read
            foreach ($unreadMessages as $message) {
                MessageReadStatus::create([
                    'message_id' => $message->message_id,
                    'reader_id' => $user->id,
                    'reader_type' => 'cose_staff',
                    'read_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'All messages marked as read',
                'count' => count($unreadMessages)
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking all messages as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all messages as read'
            ], 500);
        }
    }

    /**
     * Get users for messaging based on type.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        $type = $request->input('type', 'cose_staff');
        $users = [];
        $currentUser = Auth::user();
        
        try {
            // Log the request for debugging
            Log::info('GetUsers request', [
                'type' => $type,
                'user_id' => $currentUser->id,
                'role_id' => $currentUser->role_id
            ]);
            
            if ($type == 'cose_staff') {
                // This part was working correctly - COSE staff
                $staffQuery = User::where('status', 'Active')
                    ->where('id', '!=', $currentUser->id)
                    ->orderBy('first_name');
                
                // Apply role filtering...
                if ($currentUser->role_id == 1) {
                    $staffQuery->whereIn('role_id', [1, 2]);
                } elseif ($currentUser->role_id == 2) {
                    $staffQuery->whereIn('role_id', [1, 2, 3]);
                } elseif ($currentUser->role_id == 3) {
                    $staffQuery->where('role_id', 2);
                }
                
                $staffUsers = $staffQuery->get(['id', 'first_name', 'last_name', 'email', 'mobile', 'role_id']);
                
                foreach ($staffUsers as $user) {
                    $roleLabel = '';
                    if ($user->role_id == 1) $roleLabel = '(Admin)';
                    else if ($user->role_id == 2) $roleLabel = '(Care Manager)';
                    else if ($user->role_id == 3) $roleLabel = '(Care Worker)';
                    
                    $users[] = [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name . ' ' . $roleLabel,
                        'email' => $user->email,
                        'mobile' => $user->mobile
                    ];
                }
            } 
            else if ($type == 'beneficiary') {
                // For care workers only - only they should message beneficiaries
                if ($currentUser->role_id == 3) {
                    Log::info('Fetching beneficiaries for care worker ID: ' . $currentUser->id);
                    
                    // FIXED: Query that joins beneficiaries with general_care_plans 
                    // Removed the "status" filter since that column doesn't exist
                    $beneficiaries = DB::table('beneficiaries AS b')
                        ->join('general_care_plans AS gcp', 'b.general_care_plan_id', '=', 'gcp.general_care_plan_id')
                        ->where('gcp.care_worker_id', $currentUser->id)
                        // Use proper column name from the migration
                        ->where('b.beneficiary_status_id', '=', 1) // Uncomment if you want to filter by status ID
                        ->select('b.*')  // Select all beneficiary fields
                        ->get();
                    
                    Log::info('Found ' . count($beneficiaries) . ' beneficiaries through care plan relationship');
                    
                    foreach ($beneficiaries as $beneficiary) {
                        $users[] = [
                            'id' => $beneficiary->beneficiary_id,
                            'name' => $beneficiary->first_name . ' ' . $beneficiary->last_name . ' (Beneficiary)',
                            'email' => $beneficiary->email ?? '',
                            'mobile' => $beneficiary->phone_number ?? $beneficiary->mobile ?? ''
                        ];
                    }
                }
            } 
            else if ($type == 'family_member') {
                // For care workers only - only they should message family members
                if ($currentUser->role_id == 3) {
                    Log::info('Fetching family members for care worker ID: ' . $currentUser->id);
                    
                    // FIXED: First get beneficiary IDs through general care plans
                    // Removed the "status" filter since that column doesn't exist
                    $beneficiaryIds = DB::table('beneficiaries AS b')
                        ->join('general_care_plans AS gcp', 'b.general_care_plan_id', '=', 'gcp.general_care_plan_id')
                        ->where('gcp.care_worker_id', $currentUser->id)
                        ->pluck('b.beneficiary_id');
                    
                    Log::info('Found ' . count($beneficiaryIds) . ' beneficiary IDs through care plans');
                    
                    // Now get family members linked to these beneficiaries
                    // Using the correct column name from the migration: 'related_beneficiary_id'
                    if (count($beneficiaryIds) > 0) {
                        $familyMembers = DB::table('family_members')
                            ->whereIn('related_beneficiary_id', $beneficiaryIds)  // Use correct column from migration
                            ->get();
                        
                        Log::info('Found ' . count($familyMembers) . ' family members');
                        
                        foreach ($familyMembers as $familyMember) {
                            $users[] = [
                                'id' => $familyMember->family_member_id,
                                'name' => $familyMember->first_name . ' ' . $familyMember->last_name . ' (Family Member)',
                                'email' => $familyMember->email ?? '',
                                'mobile' => $familyMember->phone_number ?? $familyMember->mobile ?? ''
                            ];
                        }
                    }
                }
            }
            
            // Always return a valid response even if empty
            Log::info('Returning ' . count($users) . ' users');
            return response()->json(['users' => $users]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'type' => $type,
                'user_id' => $currentUser->id
            ]);
            
            // Make sure we always return a valid response with the expected structure
            return response()->json(['users' => [], 'error' => 'Failed to fetch users: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get conversation content via AJAX
     */
    public function getConversation(Request $request)
    {
        try {
            $conversationId = $request->input('id');
            
            // Get the conversation with participants
            $conversation = Conversation::with([
                'messages.attachments',
                'participants'
            ])->findOrFail($conversationId);
            
            // Add group composition for group chats
            if ($conversation->is_group_chat) {
                $composition = $this->getGroupComposition($conversationId);
                $conversation->has_admin = $composition['has_admin'];
                $conversation->has_care_worker = $composition['has_care_worker'];
            }
            
            // Add other_participant_name to conversation object
            if (!$conversation->is_group_chat) {
                // Get the participant who isn't the current user
                $otherParticipant = $conversation->participants()
                    ->where(function($query) {
                        $query->where('participant_id', '!=', Auth::id())
                            ->orWhere('participant_type', '!=', 'cose_staff');
                    })
                    ->whereNull('left_at')
                    ->first();
                    
                if ($otherParticipant) {
                    $conversation->other_participant_name = $this->getParticipantName($otherParticipant);
                    $conversation->other_participant_type = $otherParticipant->participant_type;
                } else {
                    $conversation->other_participant_name = 'Unknown User';
                    $conversation->other_participant_type = '';
                }
            }
            
            // Get messages for this conversation
                $messages = Message::with(['attachments', 'readStatuses'])
                ->where('conversation_id', $conversationId)
                ->orderBy('message_timestamp', 'asc')
                ->get();
            
            // Add sender name to each message
            foreach ($messages as $message) {
                if ($message->sender_type === 'cose_staff') {
                    $sender = User::find($message->sender_id);
                    $message->sender_name = $sender ? $sender->first_name . ' ' . $sender->last_name : 'Unknown Staff';
                    $message->sender_role_id = $sender ? $sender->role_id : null;
                } else if ($message->sender_type === 'beneficiary') {
                    $sender = Beneficiary::find($message->sender_id);
                    $message->sender_name = $sender ? $sender->first_name . ' ' . $sender->last_name : 'Unknown Beneficiary';
                } else if ($message->sender_type === 'family_member') {
                    $sender = FamilyMember::find($message->sender_id);
                    $message->sender_name = $sender ? $sender->first_name . ' ' . $sender->last_name : 'Unknown Family Member';
                } else {
                    $message->sender_name = 'Unknown';
                }

                if ($message->attachments && $message->attachments->count() > 0) {
                    Log::debug('Message has attachments', [
                        'message_id' => $message->message_id,
                        'attachments_count' => $message->attachments->count()
                    ]);
                    
                    // Process each attachment to ensure consistent formatting
                    foreach ($message->attachments as $attachment) {
                        // Make sure it has the correct properties
                        $attachment->file_path = str_replace('public/', '', $attachment->file_path);
                        
                        // Ensure is_image is properly set as boolean for JS use
                        if (is_string($attachment->is_image)) {
                            $attachment->is_image = $attachment->is_image === '1' || $attachment->is_image === 'true';
                        }
                        
                        // If file_type is missing, infer it from the file extension
                        if (empty($attachment->file_type)) {
                            $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                $attachment->file_type = 'image/' . $extension;
                                $attachment->is_image = true;
                            } else if ($extension === 'pdf') {
                                $attachment->file_type = 'application/pdf';
                            } else if (in_array($extension, ['doc', 'docx'])) {
                                $attachment->file_type = 'application/msword';
                            } else if (in_array($extension, ['xls', 'xlsx'])) {
                                $attachment->file_type = 'application/vnd.ms-excel';
                            } else if ($extension === 'txt') {
                                $attachment->file_type = 'text/plain';
                            } else {
                                $attachment->file_type = 'application/octet-stream';
                            }
                        }
                    }
                }
            }

            // Determine the correct view prefix based on user role
            $viewPrefix = $this->getRoleViewPrefix();
            
            // Render conversation content HTML
            $html = view($viewPrefix . '.conversation-content', [
                'conversation' => $conversation,
                'messages' => $messages,
                'rolePrefix' => $this->getRoleRoutePrefix()
            ])->render();
                        
            return response()->json([
                'html' => $html,
                'conversation' => [
                    'id' => $conversation->conversation_id,
                    'is_group' => $conversation->is_group_chat,
                    'name' => $conversation->name ?? '',
                    'has_admin' => $conversation->has_admin ?? false,
                    'has_care_worker' => $conversation->has_care_worker ?? false
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading conversation: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error loading conversation: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Allow a user to leave a group conversation
     */
    public function leaveGroupConversation(Request $request)
    {
        try {
            $user = Auth::user();
            $conversationId = $request->input('conversation_id');
            
            $conversation = Conversation::findOrFail($conversationId);
            
            // Ensure it's a group chat
            if (!$conversation->is_group_chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot leave a private conversation'
                ], 400);
            }
            
            // Find the participant record
            $participant = ConversationParticipant::where('conversation_id', $conversationId)
                ->where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->first();
            
            if (!$participant) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a participant in this conversation'
                ], 404);
            }
            
            // Get user's full name and role
            $userName = $user->first_name . ' ' . $user->last_name;
            $userRole = '';
            if ($user->role_id == 1) {
                $userRole = 'Administrator';
            } elseif ($user->role_id == 2) {
                $userRole = 'Care Manager';
            } elseif ($user->role_id == 3) {
                $userRole = 'Care Worker';
            }
            
            // Create a system message indicating the user left
            $message = new Message([
                'conversation_id' => $conversationId,
                'sender_id' => null,  // System message has no sender
                'sender_type' => 'system',
                'content' => "{$userName} ({$userRole}) left the group",
                'message_timestamp' => now(),
            ]);
            $message->save();
            
            // Update last message in conversation
            $conversation->last_message_id = $message->message_id;
            $conversation->save();
            
            // Update the participant record with left_at timestamp
            $participant->left_at = now();
            $participant->save();
            
            return response()->json([
                'success' => true,
                'message' => 'You have left the group conversation'
            ]);
        } catch (\Exception $e) {
            Log::error('Error leaving group conversation: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error leaving group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all participant names for a conversation for search
     */
    private function getAllParticipantNames($conversation)
    {
        $names = [];
        
        foreach ($conversation->participants as $participant) {
            if ($participant->participant_type === 'cose_staff') {
                $user = User::find($participant->participant_id);
                if ($user) {
                    $names[] = $user->first_name . ' ' . $user->last_name;
                }
            } else if ($participant->participant_type === 'beneficiary') {
                $beneficiary = Beneficiary::find($participant->participant_id);
                if ($beneficiary) {
                    $names[] = $beneficiary->first_name . ' ' . $beneficiary->last_name;
                }
            } else if ($participant->participant_type === 'family_member') {
                $familyMember = FamilyMember::find($participant->participant_id);
                if ($familyMember) {
                    $names[] = $familyMember->first_name . ' ' . $familyMember->last_name;
                }
            }
        }
        
        return implode(', ', $names);
    }

    /**
     * Get conversations list via AJAX to refresh the sidebar
     */
    public function getConversationsList(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get conversations
            $conversations = $this->getUserConversations($user);
            
            // Process participant names for display
            foreach ($conversations as $conversation) {
                if (!$conversation->is_group_chat) {
                    // Find the other participant
                    $otherParticipant = $conversation->participants()
                        ->where(function($query) use ($user) {
                            $query->where('participant_id', '!=', $user->id)
                                ->orWhere('participant_type', '!=', 'cose_staff');
                        })
                        ->whereNull('left_at')
                        ->first();
                    
                    if ($otherParticipant) {
                        $conversation->other_participant_name = $this->getParticipantName($otherParticipant);
                        $conversation->other_participant_type = $otherParticipant->participant_type;
                    } else {
                        $conversation->other_participant_name = 'Unknown User';
                        $conversation->other_participant_type = '';
                    }
                }
            }
            
            // Render conversation list HTML
            $html = view('admin.conversation-list', [
                'conversations' => $conversations
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting conversations list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading conversations: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check if conversation exists with a specific recipient.
     */
    public function getConversationsWithRecipient(Request $request)
    {
        try {
            $userId = Auth::id();
            
            // Accept multiple parameter formats for flexibility
            $recipientId = $request->input('recipient_id') ?? $request->input('participant_id');
            $recipientType = $request->input('recipient_type') ?? $request->input('participant_type');
            
            Log::debug('Checking for existing conversation', [
                'user_id' => $userId,
                'recipient_id' => $recipientId,
                'recipient_type' => $recipientType,
                'request_data' => $request->all()
            ]);
            
            if (!$recipientId || !$recipientType) {
                return response()->json(['exists' => false]);
            }
            
            // Find existing conversation
            $existingConversation = $this->findExistingConversation($userId, $recipientId, $recipientType);
            
            if ($existingConversation) {
                return response()->json([
                    'exists' => true,
                    'conversation_id' => $existingConversation->conversation_id
                ]);
            } else {
                return response()->json(['exists' => false]);
            }
        } catch (\Exception $e) {
            Log::error('Error checking for existing conversation: ' . $e->getMessage());
            return response()->json(['exists' => false, 'error' => $e->getMessage()]);
        }
    }

    public function checkLastParticipant($id)
    {
        try {
            $user = Auth::user();
            $conversation = Conversation::findOrFail($id);
            
            // Only apply to group chats
            if (!$conversation->is_group_chat) {
                return response()->json(['is_last' => false]);
            }
            
            // Count active participants
            $participantCount = ConversationParticipant::where('conversation_id', $id)
                ->count();
            
            // Return if this is the last participant
            return response()->json([
                'is_last' => $participantCount <= 1,
                'count' => $participantCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking if last participant: ' . $e->getMessage());
            return response()->json([
                'is_last' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Leave a conversation
     */
    public function leaveConversation(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,conversation_id',
            ]);
            
            $user = Auth::user();
            $conversationId = $request->conversation_id;
            $isLastParticipant = false;
            
            // Get the conversation
            $conversation = Conversation::findOrFail($conversationId);
            
            // Check if this is a group chat
            if ($conversation->is_group_chat) {
                // Count participants before leaving
                $participantCount = ConversationParticipant::where('conversation_id', $conversationId)
                    ->count();
                
                $isLastParticipant = ($participantCount <= 1);
            }
            
            // If this is the last participant, delete the entire conversation
            if ($isLastParticipant) {
                // Get all message IDs in this conversation
                $messageIds = Message::where('conversation_id', $conversationId)
                    ->pluck('message_id')
                    ->toArray();
                
                // Delete read statuses for these messages
                if (!empty($messageIds)) {
                    MessageReadStatus::whereIn('message_id', $messageIds)->delete();
                }
                
                // Delete message attachments if any
                MessageAttachment::whereIn('message_id', $messageIds)->delete();
                
                // Delete messages
                Message::where('conversation_id', $conversationId)->delete();
                
                // Delete participants
                ConversationParticipant::where('conversation_id', $conversationId)->delete();
                
                // Finally delete the conversation
                $conversation->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'You were the last member. Group has been deleted.',
                    'was_last' => true
                ]);
            } else {
                // Just remove this participant
                ConversationParticipant::where('conversation_id', $conversationId)
                    ->where('participant_id', $user->id)
                    ->where('participant_type', 'cose_staff')
                    ->delete();
                
                // Add system message about user leaving
                $message = new Message([
                    'conversation_id' => $conversationId,
                    'sender_id' => 0,
                    'sender_type' => 'system',
                    'content' => $user->first_name . ' ' . $user->last_name . ' left the group',
                    'message_timestamp' => now(),
                ]);
                $message->save();
                
                // Update last message in conversation
                $conversation->last_message_id = $message->message_id;
                $conversation->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'You have left the conversation',
                    'was_last' => false
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error leaving conversation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not leave conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get members of a group conversation
     */
    public function getGroupMembers($id)
    {
        try {
            $user = Auth::user();
            $conversation = Conversation::findOrFail($id);
            
            // Check if this is a group chat and user is a participant
            if (!$conversation->is_group_chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'This is not a group conversation'
                ], 400);
            }
            
            $isParticipant = ConversationParticipant::where('conversation_id', $id)
                ->where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->exists();
                
            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }
            
            // Get all participants
            $participants = ConversationParticipant::where('conversation_id', $id)
                ->whereNull('left_at')
                ->orderBy('joined_at', 'asc')
                ->get();
            
            $members = [];
            
            foreach ($participants as $participant) {
                $member = [
                    'participant_id' => $participant->participant_id,
                    'participant_type' => $participant->participant_type,
                    'joined_at' => $participant->joined_at->format('Y-m-d H:i:s'),
                ];
                
                // Get participant details based on type
                if ($participant->participant_type === 'cose_staff') {
                    $staff = User::find($participant->participant_id);
                    if ($staff) {
                        $member['name'] = $staff->first_name . ' ' . $staff->last_name;
                        $member['email'] = $staff->email;
                        $member['role_id'] = $staff->role_id;
                    }
                } elseif ($participant->participant_type === 'beneficiary') {
                    $beneficiary = Beneficiary::find($participant->participant_id);
                    if ($beneficiary) {
                        $member['name'] = $beneficiary->first_name . ' ' . $beneficiary->last_name;
                    }
                } elseif ($participant->participant_type === 'family_member') {
                    $familyMember = FamilyMember::find($participant->participant_id);
                    if ($familyMember) {
                        $member['name'] = $familyMember->first_name . ' ' . $familyMember->last_name;
                        $member['email'] = $familyMember->email;
                    }
                }
                
                $members[] = $member;
            }
            
            return response()->json([
                'success' => true,
                'conversation_id' => $id,
                'conversation_name' => $conversation->name,
                'members' => $members
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting group members: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not load group members: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a member to a group conversation
     */
    public function addGroupMember(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Validate request
            $validatedData = $request->validate([
                'conversation_id' => 'required|integer',
                'participant_id' => 'required',
                'participant_type' => 'required|in:cose_staff,beneficiary,family_member',
            ]);
            
            $conversation = Conversation::findOrFail($validatedData['conversation_id']);
            
            // Verify this is a group chat
            if (!$conversation->is_group_chat) {
                return $this->jsonResponse(false, 'This is not a group conversation', 400);
            }
            
            // Check if user is a participant
            $isParticipant = $conversation->participants()
                ->where('participant_id', $user->id)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->exists();
                
            if (!$isParticipant) {
                return $this->jsonResponse(false, 'You are not a participant in this conversation', 403);
            }
            
            // Check if member is already in the conversation
            $alreadyMember = $conversation->participants()
                ->where('participant_id', $validatedData['participant_id'])
                ->where('participant_type', $validatedData['participant_type'])
                ->whereNull('left_at')
                ->exists();
                
            if ($alreadyMember) {
                return $this->jsonResponse(false, 'This user is already a member of the conversation', 400);
            }
            
            // VALIDATION RULES FOR INCOMPATIBLE ROLES
            if ($user->role_id == 2 && $validatedData['participant_type'] === 'cose_staff') { // Care Manager adding staff
                $newMemberUser = User::findOrFail($validatedData['participant_id']);
                
                // Check if adding a Care Worker (role 3) while an Admin (role 1) is already in the group
                if ($newMemberUser->role_id == 3) { // Adding a Care Worker
                    // Check if any Admin exists in the group
                    $hasAdmin = false;
                    
                    // Get all staff participants
                    $staffParticipants = $conversation->participants()
                        ->where('participant_type', 'cose_staff')
                        ->whereNull('left_at')
                        ->get();
                        
                    // Check if any of them are admins
                    foreach ($staffParticipants as $participant) {
                        $staffUser = User::find($participant->participant_id);
                        if ($staffUser && $staffUser->role_id == 1) { // Admin role
                            $hasAdmin = true;
                            break;
                        }
                    }
                    
                    if ($hasAdmin) {
                        return $this->jsonResponse(false, 'Care Workers cannot be added to a group that includes Administrators', 400);
                    }
                }
                
                // Check if adding an Admin (role 1) while a Care Worker (role 3) is already in the group
                if ($newMemberUser->role_id == 1) { // Adding an Admin
                    // Check if any Care Worker exists in the group
                    $hasCareWorker = false;
                    
                    // Get all staff participants
                    $staffParticipants = $conversation->participants()
                        ->where('participant_type', 'cose_staff')
                        ->whereNull('left_at')
                        ->get();
                        
                    // Check if any of them are care workers
                    foreach ($staffParticipants as $participant) {
                        $staffUser = User::find($participant->participant_id);
                        if ($staffUser && $staffUser->role_id == 3) { // Care Worker role
                            $hasCareWorker = true;
                            break;
                        }
                    }
                    
                    if ($hasCareWorker) {
                        return $this->jsonResponse(false, 'Administrators cannot be added to a group that includes Care Workers', 400);
                    }
                }
            }
            
            // Now add the participant
            $participant = new ConversationParticipant([
                'conversation_id' => $validatedData['conversation_id'],
                'participant_id' => $validatedData['participant_id'],
                'participant_type' => $validatedData['participant_type'],
                'joined_at' => now(),
            ]);
            $participant->save();
            
            // Create system message for the new member
            $userName = '';
            if ($validatedData['participant_type'] === 'cose_staff') {
                $memberUser = User::find($validatedData['participant_id']);
                $userName = $memberUser ? $memberUser->first_name . ' ' . $memberUser->last_name : 'Unknown User';
            } elseif ($validatedData['participant_type'] === 'beneficiary') {
                $beneficiary = Beneficiary::find($validatedData['participant_id']);
                $userName = $beneficiary ? $beneficiary->first_name . ' ' . $beneficiary->last_name : 'Unknown Beneficiary';
            } elseif ($validatedData['participant_type'] === 'family_member') {
                $familyMember = FamilyMember::find($validatedData['participant_id']);
                $userName = $familyMember ? $familyMember->first_name . ' ' . $familyMember->last_name : 'Unknown Family Member';
            }
            
            // Create system message
            $message = new Message([
                'conversation_id' => $validatedData['conversation_id'],
                'sender_id' => 0,
                'sender_type' => 'system',
                'content' => $userName . ' has joined the group.',
                'message_timestamp' => now(),
            ]);
            $message->save();
            
            // Update last message in conversation
            $conversation->last_message_id = $message->message_id;
            $conversation->updated_at = now();
            $conversation->save();
            
            return $this->jsonResponse(true, 'Member added successfully', 200, [
                'participant' => $participant,
                'name' => $userName
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error adding group member: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to add member: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get the composition of a group (has admin, has care worker)
     */
    private function getGroupComposition($conversationId)
    {
        $hasAdmin = false;
        $hasCareWorker = false;
        
        // Get all staff participants
        $staffParticipants = ConversationParticipant::where('conversation_id', $conversationId)
            ->where('participant_type', 'cose_staff')
            ->whereNull('left_at')
            ->get();
            
        // Check participants' roles
        foreach ($staffParticipants as $participant) {
            $staffUser = User::find($participant->participant_id);
            if ($staffUser) {
                if ($staffUser->role_id == 1) {
                    $hasAdmin = true;
                } else if ($staffUser->role_id == 3) {
                    $hasCareWorker = true;
                }
            }
        }
        
        return [
            'has_admin' => $hasAdmin,
            'has_care_worker' => $hasCareWorker
        ];
    }

    /**
     * Unsend (soft delete) a message
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsendMessage($id)
    {
        try {
            $message = Message::findOrFail($id);
            
            // Check if the user is the sender of this message
            if ($message->sender_id != Auth::id() || $message->sender_type != 'cose_staff') {
                return $this->jsonResponse(false, 'You can only unsend your own messages', 403);
            }
            
            // Check if message is too old to unsend (e.g., 24 hours limit)
            if (now()->diffInHours($message->message_timestamp) > 24) {
                return $this->jsonResponse(false, 'Messages can only be unsent within 24 hours of sending', 403);
            }
            
            // Mark message as unsent
            $message->is_unsent = true;
            $message->save();
            
            return $this->jsonResponse(true, 'Message unsent successfully');
        } catch (\Exception $e) {
            Log::error('Error unsending message: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->jsonResponse(false, 'Error unsending message');
        }
    }

}