<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessagingApiController extends Controller
{
    /**
     * Create a new conversation (thread).
     * POST /messaging/thread
     * Request: { name (optional), is_group_chat, user_ids: [int, ...] }
     */
    public function createThread(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'name' => 'nullable|string|max:255',
            'is_group_chat' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $conversation = Conversation::create([
                'name' => $request->name,
                'is_group_chat' => $request->is_group_chat,
            ]);

            // Add participants (including the creator)
            $participantIds = array_unique(array_merge($request->user_ids, [$request->user()->id]));
            foreach ($participantIds as $userId) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->conversation_id,
                    'participant_id' => $userId,
                    'participant_type' => 'cose_user', // adjust if you support other types
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $conversation->fresh('participants')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create conversation.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all conversations (threads) for the authenticated user.
     * GET /messaging/thread
     */
    public function listThreads(Request $request)
    {
        $user = $request->user();

        $threads = Conversation::whereHas('participants', function ($q) use ($user) {
                $q->where('participant_id', $user->id)
                  ->where('participant_type', 'cose_user');
            })
            ->with(['participants', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $threads
        ]);
    }

    /**
     * Get all messages for a conversation (thread).
     * GET /messaging/thread/{id}/messages
     */
    public function getThreadMessages($id, Request $request)
    {
        $user = $request->user();

        // Ensure user is a participant
        $isParticipant = ConversationParticipant::where('conversation_id', $id)
            ->where('participant_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $messages = Message::where('conversation_id', $id)
            ->with(['attachments'])
            ->orderBy('message_timestamp', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Send a message to a conversation (thread).
     * POST /messaging/thread/{id}/message
     * Request: { content: string }
     */
    public function sendMessage($id, Request $request)
    {
        $user = $request->user();

        // Ensure user is a participant
        $isParticipant = ConversationParticipant::where('conversation_id', $id)
            ->where('participant_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $id,
            'sender_id' => $user->id,
            'sender_type' => 'cose_user', // adjust if needed
            'content' => $request->content,
            'message_timestamp' => now(),
        ]);

        // Optionally: update last_message_id in conversations
        Conversation::where('conversation_id', $id)->update(['last_message_id' => $message->message_id]);

        // TODO: Push to Supabase socket here

        return response()->json([
            'success' => true,
            'data' => $message
        ]);
    }

    /**
     * Delete a conversation (thread).
     * DELETE /messaging/thread
     * Request: { conversation_id: int }
     */
    public function deleteThread(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer|exists:conversations,conversation_id',
        ]);

        $user = $request->user();

        // Ensure user is a participant
        $isParticipant = ConversationParticipant::where('conversation_id', $request->conversation_id)
            ->where('participant_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Optionally: Only allow delete if user is creator or all participants agree
        // For now, allow any participant to delete for themselves (remove from participants)
        ConversationParticipant::where('conversation_id', $request->conversation_id)
            ->where('participant_id', $user->id)
            ->delete();

        // Optionally: If no participants left, delete the conversation
        $remaining = ConversationParticipant::where('conversation_id', $request->conversation_id)->count();
        if ($remaining === 0) {
            Conversation::where('conversation_id', $request->conversation_id)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted (or left) successfully.'
        ]);
    }
}
