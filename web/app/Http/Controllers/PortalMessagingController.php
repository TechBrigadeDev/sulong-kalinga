<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\MessageReadStatus;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Services\LogService;
use App\Enums\LogType;
use Illuminate\Support\Facades\Storage;

class PortalMessagingController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService = null)
    {
        $this->logService = $logService ?? app(LogService::class);
    }

    /**
     * Display the messaging interface for beneficiaries and family members
     */
    public function index()
    {
        try {
            // Determine if user is a beneficiary or family member
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $user = Auth::guard($userType)->user();
            $rolePrefix = $userType;
            
            // Get user's conversations
            $conversations = $this->getUserConversations($user, $userType);
            
            // Process participant names for private conversations and fix display issues
            foreach ($conversations as $conversation) {
                if (!$conversation->is_group_chat) {
                    // For private conversations, get the other participant's name
                    foreach ($conversation->participants as $participant) {
                        if (!($participant->participant_id == $user->getKey() && 
                            ($participant->participant_type == $userType || 
                            ($participant->participant_type == 'family_member' && $userType == 'family')))) {
                            
                            // Set the participant info explicitly
                            $conversation->other_participant_name = $this->getParticipantName($participant);
                            $conversation->other_participant_type = $participant->participant_type;
                            
                            // For COSE staff, ensure role is correctly set
                            if ($participant->participant_type === 'cose_staff') {
                                $staff = User::find($participant->participant_id);
                                if ($staff) {
                                    // Set staff role based on role_id
                                    if ($staff->role_id == 1) {
                                        $conversation->staff_role = 'Administrator';
                                    } elseif ($staff->role_id == 2) {
                                        $conversation->staff_role = 'Care Manager';
                                    } elseif ($staff->role_id == 3) {
                                        $conversation->staff_role = 'Care Worker';
                                    } else {
                                        $conversation->staff_role = 'Staff';
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
                
                // Set message preview format for current user's messages
                if ($conversation->lastMessage) {
                    $isSentByCurrentUser = ($conversation->lastMessage->sender_id == $user->getKey() && 
                                ($conversation->lastMessage->sender_type == $userType || 
                                ($conversation->lastMessage->sender_type == 'family_member' && $userType == 'family')));
                    
                    $conversation->sent_by_current_user = $isSentByCurrentUser;
                }
                
                // Force accurate unread count calculation
                $unreadCount = Message::where('conversation_id', $conversation->conversation_id)
                    ->where(function($query) use ($user, $userType) {
                        // Only count messages not sent by current user
                        $query->where('sender_id', '!=', $user->getKey())
                            ->orWhere('sender_type', '!=', $userType);
                    })
                    ->whereDoesntHave('readStatuses', function($query) use ($user, $userType) {
                        $query->where('reader_id', $user->getKey())
                            ->where('reader_type', $userType);
                    })
                    ->where('is_unsent', false)
                    ->count();
                    
                $conversation->unread_count = $unreadCount;
                $conversation->has_unread = $unreadCount > 0;
            }
            
            // Get beneficiary info for later use
            $beneficiary = null;
            $assignedCareWorkerId = null;
            
            if ($userType === 'beneficiary') {
                $beneficiary = $user;
            } else {
                $beneficiary = Beneficiary::find($user->related_beneficiary_id);
            }
            
            // Get assigned care worker from beneficiary's general care plan
            if ($beneficiary && $beneficiary->general_care_plan_id) {
                $generalCarePlan = DB::table('general_care_plans')
                    ->where('general_care_plan_id', $beneficiary->general_care_plan_id)
                    ->first();
                    
                if ($generalCarePlan) {
                    $assignedCareWorkerId = $generalCarePlan->care_worker_id;
                    
                    // Get care worker details
                    $assignedCareWorker = User::find($assignedCareWorkerId);
                    if ($assignedCareWorker) {
                        $careWorkerName = $assignedCareWorker->first_name . ' ' . $assignedCareWorker->last_name;
                    }
                }
            }
            
            // Return the view with the data
            return view($userType . 'Portal.messaging', [
                'conversations' => $conversations,
                'rolePrefix' => $rolePrefix,
                'assignedCareWorkerId' => $assignedCareWorkerId ?? null,
                'assignedCareWorkerName' => $careWorkerName ?? 'No assigned care worker'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in portal messaging index: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            return view($userType . 'Portal.messaging', [
                'conversations' => collect([]),
                'rolePrefix' => $userType,
                'error' => 'Unable to load conversations. Please try again later.'
            ]);
        }

        // DEBUG: Log conversation badge details
        foreach ($conversations as $conversation) {
            if (!$conversation->is_group_chat) {
                Log::debug('Conversation Badge Info', [
                    'conversation_id' => $conversation->conversation_id,
                    'other_participant_type' => $conversation->other_participant_type ?? 'not set',
                    'staff_role' => $conversation->staff_role ?? 'not set',
                    'participant_type_display' => $conversation->participant_type_display ?? 'not set'
                ]);
            }
        }
    }
    
    private function getUserConversations($user, $userType)
    {
        // CRITICAL FIX: Handle family vs family_member type mismatch
        $participantTypes = [$userType];
        if ($userType === 'family') {
            $participantTypes[] = 'family_member';
        }

        // First, get the conversation IDs to avoid eager loading errors
        $conversationIds = Conversation::whereHas('participants', function($query) use ($user, $participantTypes) {
            $query->where('participant_id', $user->getKey())
                ->whereIn('participant_type', $participantTypes)
                ->whereNull('left_at');
        })->pluck('conversation_id');
        
        // Then load conversations with safer eager loading
        $conversations = Conversation::whereIn('conversation_id', $conversationIds)
            ->with([
                'participants',
                'lastMessage',
                'messages' => function($query) {
                    $query->orderBy('message_timestamp', 'desc')->limit(1);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Process each conversation immediately after loading
        foreach ($conversations as $conversation) {
            // For non-group conversations, explicitly check participant types
            if (!$conversation->is_group_chat) {
                // Find the other participant (not the current user)
                $otherParticipant = null;
                
                foreach ($conversation->participants as $participant) {
                    // If this is NOT the current user
                    if (!($participant->participant_id == $user->getKey() && 
                        ($participant->participant_type == $userType ||
                        ($participant->participant_type == 'family_member' && $userType == 'family')))) {
                        
                        $otherParticipant = $participant;
                        $conversation->other_participant_type = $participant->participant_type;
                        
                        // CRITICAL FIX: For COSE staff, always explicitly set the display value first
                        if ($participant->participant_type === 'cose_staff') {
                            // Default to Care Worker first for immediate display
                            $conversation->participant_type_display = 'Care Worker';
                            
                            try {
                                $staff = User::find($participant->participant_id);
                                if ($staff) {
                                    // Set display name
                                    $conversation->other_participant_name = $staff->first_name . ' ' . $staff->last_name;
                                    
                                    // Set staff role based on role_id
                                    if ($staff->role_id == 1) {
                                        $conversation->staff_role = 'Administrator';
                                        $conversation->participant_type_display = 'Administrator';
                                    } elseif ($staff->role_id == 2) {
                                        $conversation->staff_role = 'Care Manager';
                                        $conversation->participant_type_display = 'Care Manager';
                                    } else {
                                        // Keep as Care Worker for other role IDs
                                        $conversation->staff_role = 'Care Worker';
                                        $conversation->participant_type_display = 'Care Worker';
                                    }
                                }
                            } catch (\Exception $e) {
                                // Fall back to Care Worker if there's any error
                                Log::error('Error fetching staff details: ' . $e->getMessage());
                            }
                        } elseif ($participant->participant_type === 'beneficiary') {
                            // For beneficiary participants
                            $beneficiary = Beneficiary::find($participant->participant_id);
                            if ($beneficiary) {
                                $conversation->other_participant_name = $beneficiary->first_name . ' ' . $beneficiary->last_name;
                                $conversation->participant_type_display = 'Beneficiary';
                            }
                        } elseif ($participant->participant_type === 'family_member') {
                            // For family member participants
                            $familyMember = FamilyMember::find($participant->participant_id);
                            if ($familyMember) {
                                $conversation->other_participant_name = $familyMember->first_name . ' ' . $familyMember->last_name;
                                $conversation->participant_type_display = 'Family Member';
                            }
                        }
                        
                        // Once we find the other participant, break the loop
                        break;
                    }
                }
            }
            
            // Calculate unread count correctly
            $unreadCount = Message::where('conversation_id', $conversation->conversation_id)
                ->where(function($query) use ($user, $userType) {
                    // Only count messages not sent by current user
                    $query->where(function($q) use ($user, $userType) {
                        $q->where('sender_id', '!=', $user->getKey())
                        ->orWhere('sender_type', '!=', $userType);
                    });
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user, $userType) {
                    $query->where('reader_id', $user->getKey())
                        ->where('reader_type', $userType);
                })
                ->where('is_unsent', false)
                ->count();
                
            $conversation->unread_count = $unreadCount;
            $conversation->has_unread = $unreadCount > 0;
        }
        
        return $conversations;
    }

    /**
     * View a specific conversation
     */
    public function viewConversation($id)
    {
        try {
            // Determine if user is a beneficiary or family member
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $user = Auth::guard($userType)->user();
            $rolePrefix = $userType;
            
            // Get the conversation
            $conversation = Conversation::with([
                'messages.attachments',
                'messages.readStatuses'
            ])->findOrFail($id);
            
            // Security check: Make sure user is a participant
            // CRITICAL FIX: For family users, check both 'family' and 'family_member' types
            $isParticipant = false;
            
            if ($userType === 'family') {
                // Check for both family and family_member types
                $isParticipant = $conversation->participants()
                    ->where('participant_id', $user->getKey())
                    ->whereIn('participant_type', ['family', 'family_member'])
                    ->whereNull('left_at')
                    ->exists();
            } else {
                // For beneficiary, use standard check
                $isParticipant = $conversation->hasParticipant($user->getKey(), $userType);
            }
            
            if (!$isParticipant) {
                Log::warning('Unauthorized conversation view attempt', [
                    'user_id' => $user->getKey(),
                    'user_type' => $userType,
                    'conversation_id' => $id
                ]);
                
                if (request()->ajax()) {
                    return response()->json(['error' => 'You are not a participant in this conversation'], 403);
                }
                
                return redirect()->route($rolePrefix . '.messaging.index')
                    ->with('error', 'You are not a participant in this conversation.');
            }
            
            // Add other_participant_name to conversation object for display
            if (!$conversation->is_group_chat) {
                $otherParticipant = $conversation->participants
                    ->where('participant_type', '!=', $userType)
                    ->where('participant_id', '!=', $user->getKey())
                    ->first();
                
                if ($otherParticipant) {
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
                if ($message->sender_id != $user->getKey() || $message->sender_type != $userType) {
                    $this->markMessageAsRead($message->message_id, $user->getKey(), $userType);
                }
            }
            
            // Get all conversations for the sidebar
            $conversations = $this->getUserConversations($user, $userType);
            
            // Process participant names for private conversations
            foreach ($conversations as $convo) {
                if (!$convo->is_group_chat) {
                    foreach ($convo->participants as $participant) {
                        if (!($participant->participant_id == $user->getKey() && $participant->participant_type == $userType)) {
                            $convo->other_participant_name = $this->getParticipantName($participant);
                            break;
                        }
                    }
                }
            }
            
            // Check if the request is AJAX
            if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'html' => view($userType . 'Portal.conversation-content', compact('conversation', 'messages', 'rolePrefix'))->render()
                ]);
            }
            
            // For normal requests, redirect to the messaging index page with conversation ID
            return redirect()->route($rolePrefix . '.messaging.index', ['conversation' => $id]);
            
        } catch (\Exception $e) {
            Log::error('Error viewing portal conversation: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            if (request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading conversation: ' . $e->getMessage()
                ], 500);
            }
            
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            return redirect()->route($userType . '.messaging.index')
                ->with('error', 'Unable to view conversation: ' . $e->getMessage());
        }
    }

    /**
     * Send a message via AJAX or form submission
     */
    public function sendMessage(Request $request)
    {
        // Determine if user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        $user = Auth::guard($userType)->user();
        
        // ADDED: Initialize participant type variable to prevent undefined variable error
        $participantType = $userType;
        
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'content' => 'nullable|string|max:10000',
                'conversation_id' => 'required|integer|exists:conversations,conversation_id',
                'attachments.*' => 'sometimes|file|max:5120|mimes:jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $conversationId = $request->conversation_id;
            $conversation = Conversation::findOrFail($conversationId);
            
            // Security check: Make sure user is a participant
            // CRITICAL FIX: For family users, check both 'family' and 'family_member' types
            $isParticipant = false;
            
            if ($userType === 'family') {
                // Check for both family and family_member types
                $isParticipant = $conversation->participants()
                    ->where('participant_id', $user->getKey())
                    ->whereIn('participant_type', ['family', 'family_member'])
                    ->whereNull('left_at')
                    ->exists();
                    
                // ADDED: Make sure to consistently use 'family_member' as the participant type
                $participantType = 'family_member';
            } else {
                // For beneficiary, use standard check
                $isParticipant = $conversation->hasParticipant($user->getKey(), $userType);
            }
            
            if (!$isParticipant) {
                return response()->json(['message' => 'You are not a participant in this conversation'], 403);
            }
            
            // Now create the message
            $message = new Message();
            $message->conversation_id = $conversationId;
            $message->sender_id = $user->getKey();
            $message->sender_type = $participantType; // Now safely uses the defined variable
            $message->content = $request->content;
            $message->message_timestamp = now();
            $message->is_unsent = false;
            $message->save();
            
            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $fileType = $file->getMimeType();
                    $fileSize = $file->getSize();
                    $isImage = strpos($fileType, 'image/') === 0;
                    
                    // Generate a unique filename to prevent overwriting
                    $uniqueFileName = Str::uuid() . '_' . $fileName;
                    
                    // Store the file in the attachments directory
                    $filePath = $file->storeAs('attachments', $uniqueFileName, 'public');
                    
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
                    
                    Log::info("File attachment created", [
                        'message_id' => $message->message_id,
                        'file_name' => $fileName,
                        'file_path' => $filePath
                    ]);
                }
            }
            
            // Update conversation's last message
            $conversation->last_message_id = $message->message_id;
            $conversation->updated_at = now();
            $conversation->save();
            
            // Return success response
            if ($request->ajax() || $request->wantsJson()) {
                // Load the message with attachments
                $message->load('attachments');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => $message
                ]);
            }
            
            return redirect()->route($rolePrefix . '.messaging.index', ['conversation' => $conversationId])
                ->with('success', 'Message sent successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error sending portal message: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error sending message: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error sending message: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Create a private conversation with the assigned care worker only
     */
    public function createConversation(Request $request)
    {
        // Determine if user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        $user = Auth::guard($userType)->user();
        $rolePrefix = $userType;
        
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'recipient_id' => 'required|exists:cose_users,id',
                'user_type' => 'sometimes|in:cose_staff'
            ]);
            
            if ($validator->fails()) {
                return $this->jsonResponse(false, 'Validation failed: ' . $validator->errors()->first(), 422);
            }
            
            // Get recipient details
            $careWorkerId = $request->recipient_id;
            $careWorker = User::find($careWorkerId);
            
            if (!$careWorker) {
                return $this->jsonResponse(false, 'Care worker not found', 404);
            }
            
            // Verify this is the assigned care worker
            $beneficiary = null;
            if ($userType === 'beneficiary') {
                $beneficiary = $user;
            } else {
                $beneficiary = Beneficiary::find($user->related_beneficiary_id);
            }
            
            if (!$beneficiary || !$beneficiary->general_care_plan_id) {
                return $this->jsonResponse(false, 'No care plan found for beneficiary', 404);
            }
            
            $generalCarePlan = DB::table('general_care_plans')
                ->where('general_care_plan_id', $beneficiary->general_care_plan_id)
                ->first();
                
            if (!$generalCarePlan || $generalCarePlan->care_worker_id != $careWorkerId) {
                return $this->jsonResponse(false, 'You can only message your assigned care worker', 403);
            }
            
            // IMPROVED LOGIC: Check for existing conversations more carefully
            $existingConversation = $this->findExistingPrivateConversation($user->getKey(), $userType, $careWorkerId);
            
            if ($existingConversation) {
                // Create initial message if provided
                if (!empty($request->initial_message)) {
                    Message::create([
                        'conversation_id' => $existingConversation->conversation_id,
                        'sender_id' => $user->getKey(),
                        'sender_type' => $userType,
                        'content' => $request->initial_message,
                        'message_timestamp' => now(),
                    ]);
                    
                    // Update conversation timestamp
                    $existingConversation->updated_at = now();
                    $existingConversation->save();
                }
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Returning existing conversation',
                        'conversation_id' => $existingConversation->conversation_id
                    ]);
                }
                
                return redirect()->route($rolePrefix . '.messaging.index', ['conversation' => $existingConversation->conversation_id]);
            }
            
            // Create new conversation
            $conversation = DB::transaction(function() use ($user, $userType, $careWorkerId, $request) {
                // Create conversation
                $conversation = Conversation::create([
                    'is_group_chat' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Add participants - FIXED WITH PROPER PARTICIPANT DETAILS
                ConversationParticipant::create([
                    'conversation_id' => $conversation->conversation_id,
                    'participant_id' => $user->getKey(),
                    'participant_type' => $userType,
                    'joined_at' => now()
                ]);
                
                ConversationParticipant::create([
                    'conversation_id' => $conversation->conversation_id,
                    'participant_id' => $careWorkerId,
                    'participant_type' => 'cose_staff',
                    'joined_at' => now()
                ]);
                
                // Add initial message if provided
                if (!empty($request->initial_message)) {
                    $message = Message::create([
                        'conversation_id' => $conversation->conversation_id,
                        'sender_id' => $user->getKey(),
                        'sender_type' => $userType,
                        'content' => $request->initial_message,
                        'message_timestamp' => now(),
                    ]);
                    
                    $conversation->last_message_id = $message->message_id;
                    $conversation->save();
                }
                
                return $conversation;
            });
            
            // Return success response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conversation created successfully',
                    'conversation_id' => $conversation->conversation_id
                ]);
            }
            
            return redirect()->route($rolePrefix . '.messaging.index', ['conversation' => $conversation->conversation_id]);
            
        } catch (\Exception $e) {
            Log::error('Error creating portal conversation: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating conversation: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error creating conversation: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Improved helper method to find existing private conversation between users
     */
    private function findExistingPrivateConversation($userId, $userType, $careWorkerId)
    {
        // Get non-group conversations where the current user is a participant
        $userConversations = Conversation::whereHas('participants', function($query) use ($userId, $userType) {
                $query->where('participant_id', $userId)
                    ->where('participant_type', $userType)
                    ->whereNull('left_at');
            })
            ->where('is_group_chat', false)
            ->get();
        
        // Now check each conversation to see if the care worker is also a participant
        foreach ($userConversations as $conversation) {
            $hasCareWorker = $conversation->participants()
                ->where('participant_id', $careWorkerId)
                ->where('participant_type', 'cose_staff')
                ->whereNull('left_at')
                ->exists();
                
            if ($hasCareWorker) {
                return $conversation;
            }
        }
        
        return null;
    }

    /**
     * Leave a group conversation
     */
    public function leaveGroupConversation(Request $request)
    {
        // Determine if user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        $user = Auth::guard($userType)->user();
        $rolePrefix = $userType;
        
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'conversation_id' => 'required|exists:conversations,conversation_id'
            ]);
            
            if ($validator->fails()) {
                return $this->jsonResponse(false, 'Invalid conversation', 422);
            }
            
            $conversationId = $request->conversation_id;
            $conversation = Conversation::findOrFail($conversationId);
            
            // Check if conversation is a group chat
            if (!$conversation->is_group_chat) {
                return $this->jsonResponse(false, 'Cannot leave a private conversation', 422);
            }
            
            // Check if user is participant
            $participant = ConversationParticipant::where('conversation_id', $conversationId)
                ->where('participant_id', $user->getKey())
                ->where('participant_type', $userType)
                ->whereNull('left_at')
                ->first();
                
            if (!$participant) {
                return $this->jsonResponse(false, 'You are not a participant in this conversation', 403);
            }
            
            // Mark participant as left
            $participant->left_at = now();
            $participant->save();
            
            // Add system message
            $message = new Message([
                'conversation_id' => $conversationId,
                'sender_type' => 'system',
                'content' => ($userType === 'beneficiary' ? 'Beneficiary ' : 'Family member ') . 
                                $user->first_name . ' ' . $user->last_name . ' left the group',
                'message_timestamp' => now(),
            ]);
            $message->save();
            
            // Update conversation's last message
            $conversation->last_message_id = $message->message_id;
            $conversation->updated_at = now();
            $conversation->save();
            
            return $this->jsonResponse(true, 'Left group successfully');
            
        } catch (\Exception $e) {
            Log::error('Error leaving portal group conversation: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->jsonResponse(false, 'Error leaving group: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Unsend (soft delete) a message
     */
    public function unsendMessage($id)
    {
        // Determine if user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        $user = Auth::guard($userType)->user();
        
        try {
            $message = Message::findOrFail($id);
            
            // Security check: Only allow unsending your own messages
            // FIXED: For family members, check both possible types
            $canUnsend = false;
            
            if ($userType === 'family') {
                // Check if sender is the current user (allowing both 'family' and 'family_member' types)
                $canUnsend = $message->sender_id == $user->getKey() && 
                            ($message->sender_type == 'family' || $message->sender_type == 'family_member');
            } else {
                // For beneficiaries, use standard check
                $canUnsend = $message->sender_id == $user->getKey() && $message->sender_type == $userType;
            }
            
            if (!$canUnsend) {
                return response()->json(['message' => 'You can only unsend your own messages'], 403);
            }
            
            // Only allow unsending recent messages (e.g., less than 1 hour old)
            $messageTime = $message->message_timestamp;
            $now = now();
            
            if ($now->diffInMinutes($messageTime) > 60) {
                return response()->json(['message' => 'Messages can only be unsent within 1 hour of sending'], 403);
            }
            
            // Mark as unsent
            $message->is_unsent = true;
            $message->save();
            
            return response()->json(['success' => true, 'message' => 'Message unsent successfully']);
            
        } catch (\Exception $e) {
            Log::error('Error unsending message: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['message' => 'Failed to unsend message'], 500);
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
            Log::error('Error marking portal message as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread message count for the current user
     */
    public function getUnreadCount()
    {
        // Determine if user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        $user = Auth::guard($userType)->user();
        
       try {

            // CRITICAL FIX: Handle family vs family_member type mismatch
            $participantTypes = [$userType];
            if ($userType === 'family') {
                $participantTypes[] = 'family_member';
            }

            // Get conversations with the user as participant
            $conversationIds = ConversationParticipant::where('participant_id', $user->getKey())
                ->where('participant_type', $userType)
                ->whereNull('left_at')
                ->pluck('conversation_id');
                
            // Count unread messages
            $unreadCount = Message::whereIn('conversation_id', $conversationIds)
                ->where(function($query) use ($user, $userType) {
                    $query->where('sender_id', '!=', $user->getKey())
                        ->orWhere('sender_type', '!=', $userType);
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user, $userType) {
                    $query->where('reader_id', $user->getKey())
                        ->where('reader_type', $userType);
                })
                ->count();
                
            return response()->json(['count' => $unreadCount]);
            
        } catch (\Exception $e) {
            Log::error('Error getting portal unread message count: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Get recent messages for dropdown menu
     */
    public function getRecentMessages()
    {
        // Determine if user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        $user = Auth::guard($userType)->user();
        
        try {
            // CRITICAL FIX: Handle family vs family_member type mismatch
            $participantTypes = [$userType];
            if ($userType === 'family') {
                $participantTypes[] = 'family_member';
            }
            
            // Get conversations with the user as participant - FIXED to use whereIn
            $conversationIds = ConversationParticipant::where('participant_id', $user->getKey())
                ->whereIn('participant_type', $participantTypes)  // FIXED: use whereIn with array
                ->whereNull('left_at')
                ->pluck('conversation_id');
                
            // Log count of conversations found for debugging
            \Log::info('Recent messages query for ' . $userType, [
                'user_id' => $user->getKey(),
                'conversation_count' => count($conversationIds),
                'conversation_ids' => $conversationIds
            ]);
            
            // Get conversations with their last messages
            $conversations = Conversation::whereIn('conversation_id', $conversationIds->all())
                ->with(['lastMessage', 'participants'])
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
            
            $unreadCount = 0;
            $recentMessages = [];
            
            foreach ($conversations as $conversation) {
                // Skip conversations with no messages
                if (!$conversation->lastMessage) continue;
                
                // Get message details
                $message = $conversation->lastMessage;
                
                // FIXED: Check if message is read using correct participant types
                $isRead = $message->readStatuses()
                    ->where('reader_id', $user->getKey())
                    ->whereIn('reader_type', $participantTypes)
                    ->exists();
                
                // FIXED: Check for unread status correctly
                $isUnread = !$isRead && 
                    !($message->sender_id == $user->getKey() && 
                    (in_array($message->sender_type, $participantTypes)));
                    
                if ($isUnread) {
                    $unreadCount++;
                }
                
                // Set display name for the conversation
                $conversationName = $conversation->is_group_chat ? 
                    $conversation->name : 
                    $this->getConversationName($conversation, $user, $userType);
                
                // Add to results
                $recentMessages[] = [
                    'conversation_id' => $conversation->conversation_id,
                    'conversation_name' => $conversationName,
                    'content' => $message->is_unsent ? 'This message was unsent' : $message->content,
                    'is_unsent' => $message->is_unsent,
                    'time_ago' => \Carbon\Carbon::parse($message->message_timestamp)->diffForHumans(),
                    'unread' => $isUnread,
                    'is_group' => $conversation->is_group_chat
                ];
            }
            
            \Log::info('Formatted recent messages', [
                'message_count' => count($recentMessages),
                'unread_count' => $unreadCount
            ]);
            
            return response()->json([
                'success' => true,
                'messages' => $recentMessages,
                'unread_count' => $unreadCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting recent messages: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'messages' => [],
                'unread_count' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Helper method to get a conversation name for display
     */
    private function getConversationName($conversation, $user, $userType)
    {
        if ($conversation->is_group_chat) {
            return $conversation->name ?: 'Group Chat';
        } else {
            // For private conversations, get the other participant's name
            $otherParticipantName = 'Unknown User';
            
            foreach ($conversation->participants as $participant) {
                if (!($participant->participant_id == $user->getKey() && 
                    ($participant->participant_type == $userType || 
                    ($userType == 'family' && $participant->participant_type == 'family_member')))) {
                    
                    $otherParticipantName = $this->getParticipantName($participant);
                    break;
                }
            }
            
            return $otherParticipantName;
        }
    }

    /**
     * Get a participant's name
     */
    private function getParticipantName($participant)
    {
        try {
            switch($participant->participant_type) {
                case 'cose_staff':
                    $user = User::find($participant->participant_id);
                    return $user ? $user->first_name . ' ' . $user->last_name : 'Unknown Staff';
                case 'beneficiary':
                    $beneficiary = Beneficiary::find($participant->participant_id);
                    return $beneficiary ? $beneficiary->first_name . ' ' . $beneficiary->last_name : 'Unknown Beneficiary';
                case 'family_member':
                    $familyMember = FamilyMember::find($participant->participant_id);
                    return $familyMember ? $familyMember->first_name . ' ' . $familyMember->last_name : 'Unknown Family Member';
                default:
                    return 'Unknown User';
            }
        } catch (\Exception $e) {
            Log::error('Error getting participant name: ' . $e->getMessage());
            return 'Unknown User';
        }
    }

    /**
     * Get a sender's name
     */
    private function getSenderName($message)
    {
        try {
            switch($message->sender_type) {
                case 'cose_staff':
                    $user = User::find($message->sender_id);
                    return $user ? $user->first_name : 'Unknown';
                case 'beneficiary':
                    $beneficiary = Beneficiary::find($message->sender_id);
                    return $beneficiary ? $beneficiary->first_name : 'Unknown';
                case 'family_member':
                    $familyMember = FamilyMember::find($message->sender_id);
                    return $familyMember ? $familyMember->first_name : 'Unknown';
                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error('Error getting sender name: ' . $e->getMessage());
            return 'Unknown';
        }
    }

    /**
     * Mark all unread messages for the current user as read
     */
    public function markAllAsRead()
    {
        // Determine if user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        $user = Auth::guard($userType)->user();
        
        try {
            // CRITICAL FIX: Handle family vs family_member type mismatch
            $participantTypes = [$userType];
            if ($userType === 'family') {
                $participantTypes[] = 'family_member';
            }
            
            // Get conversations with the user as participant
            // FIX: Use whereIn with participantTypes instead of where with userType
            $conversationIds = ConversationParticipant::where('participant_id', $user->getKey())
                ->whereIn('participant_type', $participantTypes) // FIXED: use whereIn with the array
                ->whereNull('left_at')
                ->pluck('conversation_id');
                
            // Get unread messages
            $messages = Message::whereIn('conversation_id', $conversationIds)
                ->where(function($query) use ($user, $userType, $participantTypes) {
                    // Only count messages not sent by current user
                    $query->where(function($q) use ($user, $participantTypes) {
                        $q->where('sender_id', '!=', $user->getKey())
                        ->orWhereNotIn('sender_type', $participantTypes);
                    });
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user, $userType, $participantTypes) {
                    $query->where('reader_id', $user->getKey())
                        ->whereIn('reader_type', $participantTypes); // FIXED: use whereIn here too
                })
                ->get();
                
            // Mark all as read
            foreach ($messages as $message) {
                MessageReadStatus::firstOrCreate([
                    'message_id' => $message->message_id,
                    'reader_id' => $user->getKey(),
                    'reader_type' => $userType,
                    'read_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'All messages marked as read'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error marking all portal messages as read: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error marking messages as read: ' . $e->getMessage()
            ], 500);
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
     * Get conversation details
     */
    public function getConversation(Request $request)
    {
        $id = $request->query('id');
        if (!$id) {
            return response()->json(['error' => 'No conversation ID provided'], 400);
        }
        
        try {
            // Determine if user is a beneficiary or family member
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $user = Auth::guard($userType)->user();
            $rolePrefix = $userType;
            
            // CRITICAL FIX: Handle family vs family_member type mismatch
            $participantTypes = [$userType];
            if ($userType === 'family') {
                $participantTypes[] = 'family_member';
            }
            
            // Get the conversation
            $conversation = Conversation::findOrFail($id);
            
            // Security check: Make sure user is a participant
            $isParticipant = false;
            
            if ($userType === 'family') {
                // Check for both family and family_member types
                $isParticipant = $conversation->participants()
                    ->where('participant_id', $user->getKey())
                    ->whereIn('participant_type', $participantTypes)
                    ->whereNull('left_at')
                    ->exists();
            } else {
                // For beneficiary, use standard check
                $isParticipant = $conversation->hasParticipant($user->getKey(), $userType);
            }
            
            if (!$isParticipant) {
                return response()->json(['error' => 'You are not a participant in this conversation'], 403);
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
                } elseif ($message->sender_type == 'family_member' || $message->sender_type == 'family') {
                    $sender = FamilyMember::find($message->sender_id);
                    if ($sender) {
                        $message->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    }
                }
            }
            
            // Mark all messages as read
            foreach ($messages as $message) {
                if ($message->sender_id != $user->getKey() || $message->sender_type != $userType) {
                    $this->markMessageAsRead($message->message_id, $user->getKey(), $userType);
                }
            }
            
            // Determine conversation name/other participant
            if (!$conversation->is_group_chat) {
                $otherParticipant = $conversation->participants
                    ->where('participant_type', '!=', $userType)
                    ->where('participant_id', '!=', $user->getKey())
                    ->first();
                
                if ($otherParticipant) {
                    $conversation->other_participant_name = $this->getParticipantName($otherParticipant);
                    $conversation->other_participant_type = $otherParticipant->participant_type;
                }
            }
            
            // Return rendered view
            $html = view($userType . 'Portal.conversation-content', [
                'conversation' => $conversation,
                'messages' => $messages,
                'rolePrefix' => $userType
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting conversation: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'conversation_id' => 'required|exists:conversations,conversation_id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid conversation ID'
                ], 422);
            }
            
            // Get user info
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $user = Auth::guard($userType)->user();
            
            // CRITICAL FIX: Handle family vs family_member type mismatch
            $participantTypes = [$userType];
            if ($userType === 'family') {
                $participantTypes[] = 'family_member';
            }
            
            // Get conversation
            $conversationId = $request->conversation_id;
            $conversation = Conversation::findOrFail($conversationId);
            
            // Check if user is a participant with the correct participant type handling
            $isParticipant = false;
            
            if ($userType === 'family') {
                // Check for both family and family_member types
                $isParticipant = $conversation->participants()
                    ->where('participant_id', $user->getKey())
                    ->whereIn('participant_type', $participantTypes)
                    ->whereNull('left_at')
                    ->exists();
            } else {
                // For beneficiary, use standard check
                $isParticipant = $conversation->hasParticipant($user->getKey(), $userType);
            }
            
            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a participant in this conversation'
                ], 403);
            }
            
            // Get unread messages in the conversation
            $messages = Message::where('conversation_id', $conversationId)
                ->where(function($query) use ($user, $userType) {
                    // Only count messages not sent by current user
                    $query->where('sender_id', '!=', $user->getKey())
                        ->orWhere('sender_type', '!=', $userType);
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user, $userType) {
                    $query->where('reader_id', $user->getKey())
                        ->where('reader_type', $userType);
                })
                ->get();
                
            // Mark all as read
            foreach ($messages as $message) {
                MessageReadStatus::firstOrCreate([
                    'message_id' => $message->message_id,
                    'reader_id' => $user->getKey(),
                    'reader_type' => $userType,
                ], [
                    'read_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Messages marked as read',
                'count' => $messages->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error marking messages as read: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error marking messages as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversations for the conversation list
     */
    public function getConversations(Request $request)
    {
        // Determine if user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        $user = Auth::guard($userType)->user();
        $rolePrefix = $userType;
        
        try {
            // Get user's conversations
            $conversations = $this->getUserConversations($user, $userType);
            
            // Process participant names for private conversations
            foreach ($conversations as $conversation) {
                if (!$conversation->is_group_chat) {
                    // For private conversations, get the other participant's name
                    foreach ($conversation->participants as $participant) {
                        if (!($participant->participant_id == $user->getKey() && $participant->participant_type == $userType)) {
                            $conversation->other_participant_name = $this->getParticipantName($participant);
                            $conversation->other_participant_type = $participant->participant_type;
                            break;
                        }
                    }
                }
                
                // Check for unread messages
                if ($conversation->lastMessage) {
                    $conversation->has_unread = !$conversation->lastMessage->isReadBy($user->getKey(), $userType);
                } else {
                    $conversation->has_unread = false;
                }
                
                // Get current user type for message preview
                $conversation->current_user_id = $user->getKey();
                $conversation->current_user_type = $userType;
            }
            
            // Return the rendered HTML for the conversation list
            $html = view($rolePrefix . 'Portal.conversation-list', [
                'conversations' => $conversations,
                'rolePrefix' => $rolePrefix,
                'currentUserId' => $user->getKey(),
                'currentUserType' => $userType
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting portal conversations: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading conversations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a message attachment with security checks
     */
    public function downloadAttachment($id)
    {
        try {
            // Get current user type and ID
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $user = Auth::guard($userType)->user();
            
            // Get the attachment
            $attachment = MessageAttachment::findOrFail($id);
            
            // Security check - make sure user has access to this conversation
            $message = Message::findOrFail($attachment->message_id);
            $conversation = Conversation::findOrFail($message->conversation_id);
            
            // Check if user is a participant
            $participantType = $userType === 'beneficiary' ? 'beneficiary' : 'family_member';
            $isParticipant = $conversation->hasParticipant($user->getKey(), $participantType);
            if (!$isParticipant) {
                return response()->json([
                    'success' => false, 
                    'message' => 'You do not have access to this attachment'
                ], 403);
            }
            
            // Get the file path
            $filePath = storage_path('app/public/' . $attachment->file_path);
            
            if (!file_exists($filePath)) {
                Log::error('Attachment file not found', [
                    'attachment_id' => $id,
                    'file_path' => $filePath
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Attachment file not found'
                ], 404);
            }
            
            return response()->download($filePath, $attachment->file_name);
            
        } catch (\Exception $e) {
            Log::error('Error downloading attachment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error downloading attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread count for a specific conversation
     */
    public function getConversationUnreadCount(Request $request)
    {
        try {
            $conversationId = $request->query('id');
            if (!$conversationId) {
                return response()->json(['success' => false, 'message' => 'Conversation ID required'], 400);
            }
            
            // Get current user
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $user = Auth::guard($userType)->user();
            
            // Count unread messages
            $unreadCount = Message::where('conversation_id', $conversationId)
                ->where(function($query) use ($user, $userType) {
                    // Only count messages not sent by current user
                    $query->where(function($q) use ($user, $userType) {
                        $q->where('sender_id', '!=', $user->getKey())
                        ->orWhere('sender_type', '!=', $userType);
                    });
                })
                ->whereDoesntHave('readStatuses', function($query) use ($user, $userType) {
                    $query->where('reader_id', $user->getKey())
                        ->where('reader_type', $userType);
                })
                ->where('is_unsent', false)
                ->count();
                
            return response()->json(['success' => true, 'count' => $unreadCount]);
        } catch (\Exception $e) {
            \Log::error('Error getting conversation unread count: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching unread count'], 500);
        }
    }

    /**
     * Check if conversation exists with a specific recipient
     */
    public function getConversationsWithRecipient(Request $request)
    {
        $recipientId = $request->query('recipient_id');
        $recipientType = $request->query('recipient_type', 'cose_staff');
        
        try {
            // Determine if user is a beneficiary or family member
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $user = Auth::guard($userType)->user();
            
            // Find existing conversation
            $existingConvo = $this->findExistingPrivateConversation($user->getKey(), $userType, $recipientId);
            
            return response()->json([
                'exists' => $existingConvo !== null,
                'conversation_id' => $existingConvo ? $existingConvo->conversation_id : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking conversations with recipient: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'exists' => false,
                'error' => 'Error checking conversations: ' . $e->getMessage()
            ], 500);
        }
    }
}