<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PortalNotificationsController extends Controller
{
    /**
     * Get notifications for the authenticated portal user
     */
    public function getUserNotifications(Request $request)
    {
        try {
            // Determine which guard is being used
            $user = null;
            $userType = null;

            if (Auth::guard('beneficiary')->check()) {
                $user = Auth::guard('beneficiary')->user();
                $userType = 'beneficiary';
            } else if (Auth::guard('family')->check()) {
                $user = Auth::guard('family')->user();
                $userType = 'family_member';
            }

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            
            // Get notifications for current portal user
            $notifications = Notification::where('user_id', $user->getAuthIdentifier())
                ->where('user_type', $userType)
                ->orderBy('date_created', 'desc')
                ->get();
            
            $unreadCount = $notifications->where('is_read', false)->count();
            
            // Log the view action (simplified to avoid dependency issues)
            Log::info("User {$user->getAuthIdentifier()} ({$userType}) viewed their notifications");

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
                'user_type' => $userType
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching portal notifications: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notifications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark a notification as read for a portal user
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            // Determine which guard is being used
            $user = null;
            $userType = null;

            if (Auth::guard('beneficiary')->check()) {
                $user = Auth::guard('beneficiary')->user();
                $userType = 'beneficiary';
            } else if (Auth::guard('family')->check()) {
                $user = Auth::guard('family')->user();
                $userType = 'family_member';
            }
            
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            
            // Find the notification and ensure it belongs to current user
            $notification = Notification::where('notification_id', $id)
                ->where('user_id', $user->getAuthIdentifier())
                ->where('user_type', $userType)
                ->first();
            
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }
            
            // Mark as read
            $notification->is_read = true;
            $notification->save();

            // Simple logging instead of using LogService
            Log::info("User {$user->getAuthIdentifier()} ({$userType}) marked notification #{$id} as read");
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'notification_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error marking notification as read: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark all notifications as read for a portal user
     */
    public function markAllAsRead(Request $request)
    {
        try {
            // Determine which guard is being used
            $user = null;
            $userType = null;

            if (Auth::guard('beneficiary')->check()) {
                $user = Auth::guard('beneficiary')->user();
                $userType = 'beneficiary';
            } else if (Auth::guard('family')->check()) {
                $user = Auth::guard('family')->user();
                $userType = 'family_member';
            }
            
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            
            // Update all unread notifications for this user
            $updated = Notification::where('user_id', $user->getAuthIdentifier())
                ->where('user_type', $userType)
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            // Simple logging instead of using LogService
            Log::info("User {$user->getAuthIdentifier()} ({$userType}) marked all notifications as read");

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'count' => $updated
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error marking all notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }
}