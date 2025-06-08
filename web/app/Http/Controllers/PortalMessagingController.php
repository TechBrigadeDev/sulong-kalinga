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
            
            // Process participant names for private conversations
            foreach ($conversations as $conversation) {
                if (!$conversation->is_group_chat) {
                    // For private conversations, get the other participant's name
                    foreach ($conversation->participants as $participant) {
                        if (!($participant->participant_id == $user->getKey() && $participant->participant_type == $userType)) {
                            $conversation->other_participant_name = $this->getParticipantName($participant);
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
    }
    
    /**
     * Get user's conversations
     */
    private function getUserConversations($user, $userType)
    {
        $conversations = Conversation::whereHas('participants', function($query) use ($user, $userType) {
            $query->where('participant_id', $user->getKey())
                  ->where('participant_type', $userType)
                  ->whereNull('left_at');
        })
        ->with([
            'participants',
            'lastMessage',
            'messages' => function($query) {
                $query->orderBy('message_timestamp', 'desc')->limit(1);
            },
            'messages.readStatuses' => function($query) use ($user, $userType) {
                $query->where('reader_id', $user->getKey())
                      ->where('reader_type', $userType);
            }
        ])
        ->orderBy('updated_at', 'desc')
        ->get();
        
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
                'participants',
                'messages.attachments',
                'messages.readStatuses'
            ])->findOrFail($id);
            
            // Security check: Make sure user is a participant
            $isParticipant = $conversation->hasParticipant($user->getKey(), $userType);
            if (!$isParticipant) {
                return redirect()->route($rolePrefix . '.messaging.index')
                    ->with('error', 'You do not have access to this conversation.');
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
        $rolePrefix = $userType;

        Log::info('Portal send message request', [
            'has_file_attachments' => $request->hasFile('attachments'),
            'all_files' => $request->allFiles(),
            'request_keys' => array_keys($request->all())
        ]);

        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'conversation_id' => 'required|exists:conversations,conversation_id',
                'content' => 'nullable|string|max:10000',
                'attachments.*' => 'sometimes|file|max:5120|mimes:jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt', // 5MB max
            ]);
            
            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }
                
                return back()->withErrors($validator)->withInput();
            }

            $conversationId = $request->conversation_id;
            $conversation = Conversation::findOrFail($conversationId);
            
            // Check if user is participant
            $isParticipant = $conversation->hasParticipant($user->getKey(), $userType);
            if (!$isParticipant) {
                return $this->jsonResponse(false, 'You are not a participant in this conversation', 403);
            }
            
            // Create message
            $message = new Message([
                'conversation_id' => $conversationId,
                'sender_id' => $user->getKey(),
                'sender_type' => $userType,
                'content' => $request->content,
                'message_timestamp' => now(),
            ]);
            $message->save();
            
            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $fileType = $file->getMimeType();
                    $fileSize = $file->getSize();
                    $isImage = strpos($fileType, 'image/') === 0;
                    
                    // Store file
                    $path = $file->store('public/message_attachments');
                    
                    // Create attachment record
                    MessageAttachment::create([
                        'message_id' => $message->message_id,
                        'file_name' => $fileName,
                        'file_path' => $path,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'is_image' => $isImage
                    ]);
                }
            }
            
            // Update conversation's last message
            $conversation->last_message_id = $message->message_id;
            $conversation->updated_at = now();
            $conversation->save();
            
            // Return success response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'message_id' => $message->message_id
                ]);
            }
            
            return redirect()->route($rolePrefix . '.messaging.index', ['conversation' => $conversationId])
                ->with('success', 'Message sent successfully');
                
        } catch (\Exception $e) {
            Log::error('Error sending portal message: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
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
            // Find the message
            $message = Message::findOrFail($id);
            
            // Check if user is the sender
            if ($message->sender_id != $user->getKey() || $message->sender_type != $userType) {
                return $this->jsonResponse(false, 'You can only unsend your own messages', 403);
            }
            
            // Mark as unsent
            $message->is_unsent = true;
            $message->save();
            
            return $this->jsonResponse(true, 'Message unsent successfully');
            
        } catch (\Exception $e) {
            Log::error('Error unsending portal message: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->jsonResponse(false, 'Error unsending message: ' . $e->getMessage(), 500);
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
        $rolePrefix = $userType;
        
        try {
            // Get conversations with the user as participant
            $conversationIds = ConversationParticipant::where('participant_id', $user->getKey())
                ->where('participant_type', $userType)
                ->whereNull('left_at')
                ->pluck('conversation_id');
                
            // Get recent messages from these conversations
            $messages = Message::whereIn('conversation_id', $conversationIds)
                ->with(['conversation', 'readStatuses'])
                ->orderBy('message_timestamp', 'desc')
                ->limit(5)
                ->get();
                
            $recentMessages = [];
            
            foreach ($messages as $message) {
                $conversation = $message->conversation;
                
                // Skip system messages for preview
                if ($message->sender_type === 'system') {
                    continue;
                }
                
                // Determine if message is read
                $isRead = $message->readStatuses->where('reader_id', $user->getKey())
                    ->where('reader_type', $userType)
                    ->isNotEmpty();
                    
                // Get conversation name
                $conversationName = 'Unknown';
                
                if ($conversation->is_group_chat) {
                    $conversationName = $conversation->name;
                } else {
                    // Find the other participant
                    $otherParticipant = ConversationParticipant::where('conversation_id', $conversation->conversation_id)
                        ->where(function($query) use ($user, $userType) {
                            $query->where('participant_id', '!=', $user->getKey())
                                ->orWhere('participant_type', '!=', $userType);
                        })
                        ->first();
                        
                    if ($otherParticipant) {
                        $conversationName = $this->getParticipantName($otherParticipant);
                    }
                }
                
                // Format message preview
                $messagePreview = '';
                
                if ($message->is_unsent) {
                    $messagePreview = 'This message was unsent';
                } else {
                    $senderPrefix = '';
                    
                    if ($message->sender_id == $user->getKey() && $message->sender_type == $userType) {
                        $senderPrefix = 'You: ';
                    } elseif ($conversation->is_group_chat) {
                        $senderName = $this->getSenderName($message);
                        $senderPrefix = $senderName ? $senderName . ': ' : '';
                    }
                    
                    $messagePreview = $senderPrefix . Str::limit($message->content, 30);
                }
                
                $recentMessages[] = [
                    'conversation_id' => $conversation->conversation_id,
                    'name' => $conversationName,
                    'message' => $messagePreview,
                    'timestamp' => $message->message_timestamp->diffForHumans(),
                    'is_read' => $isRead,
                    'is_group' => $conversation->is_group_chat
                ];
                
                // Limit to 5 unique conversations
                if (count($recentMessages) >= 5) {
                    break;
                }
            }
            
            return response()->json([
                'success' => true,
                'messages' => $recentMessages
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting portal recent messages: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'messages' => []
            ]);
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
            // Get conversations with the user as participant
            $conversationIds = ConversationParticipant::where('participant_id', $user->getKey())
                ->where('participant_type', $userType)
                ->whereNull('left_at')
                ->pluck('conversation_id');
                
            // Get unread messages
            $messages = Message::whereIn('conversation_id', $conversationIds)
                ->where(function($query) use ($user, $userType) {
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
                $this->markMessageAsRead($message->message_id, $user->getKey(), $userType);
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
            return response()->json([
                'success' => false,
                'message' => 'Missing conversation ID'
            ], 400);
        }
        
        try {
            // Determine if user is a beneficiary or family member
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $user = Auth::guard($userType)->user();
            $rolePrefix = $userType;
            
            // Get the conversation with all related data
            $conversation = Conversation::with([
                'participants',
                'messages.attachments',
                'messages.readStatuses'
            ])->findOrFail($id);
            
            // Security check: Make sure user is a participant
            $isParticipant = $conversation->participants->where('participant_id', $user->getKey())
                                                    ->where('participant_type', $userType)
                                                    ->where('left_at', null)
                                                    ->isNotEmpty();
            
            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this conversation'
                ], 403);
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

    /**
     * Mark messages in a conversation as read
     */
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
            
            // Get conversation
            $conversationId = $request->conversation_id;
            $conversation = Conversation::findOrFail($conversationId);
            
            // Check if user is a participant
            $isParticipant = $conversation->hasParticipant($user->getKey(), $userType);
            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a participant in this conversation'
                ], 403);
            }
            
            // Get unread messages in the conversation
            $messages = Message::where('conversation_id', $conversationId)
                ->where(function($query) use ($user, $userType) {
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
     * Download a message attachment
     */
    public function downloadAttachment($id)
    {
        try {
            // Get attachment
            $attachment = MessageAttachment::findOrFail($id);
            
            // Get the message
            $message = Message::findOrFail($attachment->message_id);
            $conversation = Conversation::findOrFail($message->conversation_id);
            
            // Check if user can access this attachment
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            $user = Auth::guard($userType)->user();
            
            // Security check: Make sure user is a participant
            $isParticipant = $conversation->participants->where('participant_id', $user->getKey())
                                                ->where('participant_type', $userType)
                                                ->where('left_at', null)
                                                ->isNotEmpty();
            
            if (!$isParticipant) {
                abort(403, 'You do not have access to this attachment');
            }
            
            // Get the file path
            $filePath = storage_path('app/public/attachments/' . $attachment->filename);
            
            // Check if file exists
            if (!file_exists($filePath)) {
                abort(404, 'File not found');
            }
            
            // Return the file for download
            return response()->download(
                $filePath, 
                $attachment->original_filename,
                ['Content-Type' => $attachment->mime_type]
            );
            
        } catch (\Exception $e) {
            Log::error('Error downloading attachment: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Error downloading attachment: ' . $e->getMessage());
        }
    }
}