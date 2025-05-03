<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Http\Controllers\NotificationsController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationsController extends Controller
{
    /**
     * Get notifications for the authenticated user
     */
    public function getUserNotifications(Request $request, $userType = null)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        /*\Log::info('User requesting notifications', [
            'user_id' => $user ? $user->id : 'null',
            'role_id' => $user ? $user->role_id : 'null',
            'user_type' => $userType
        ]);*/
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        
        try {
            // Determine notification user type based on URL or role
            $notificationType = 'cose_staff'; // Default to cose_staff
            
            // If URL contains care-manager, use that as a hint
            if ($request->is('care-manager/*') || $request->is('care-manager')) {
                $notificationType = 'cose_staff'; // Care managers still use cose_staff
            }
            
            // Get notifications for current user
            $notifications = Notification::where('user_id', $user->id)
                ->where('user_type', $notificationType)
                ->orderBy('date_created', 'desc')
                ->get();
            
            /*\Log::info('Fetched notifications', [
                'count' => $notifications->count(),
                'query' => [
                    'user_id' => $user->id,
                    'user_type' => $notificationType
                ]
            ]);*/
            
            $unreadCount = $notifications->where('is_read', false)->count();
            
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching notifications: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notifications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get notifications for the admin dashboard (all users)
     */
    public function getAllNotifications(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        // Get all notifications, newest first
        $notifications = Notification::orderBy('date_created', 'desc')
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Mark a notification as read
     * 
     * @param Request $request
     * @param int $id
     * @param string $userType (optional) Override for user type
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $id, $userType = null)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        
        $notificationUserType = $this->determineUserType($user, $userType);
        
        // Find the notification and ensure it belongs to current user
        $notification = Notification::where('notification_id', $id)
            ->where('user_id', $user->id)
            ->where('user_type', $notificationUserType)
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
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request, $userType = null)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        
        $notificationUserType = $this->determineUserType($user, $userType);
        
        // Update all unread notifications for this user
        $updated = Notification::where('user_id', $user->id)
            ->where('user_type', $notificationUserType)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'count' => $updated
        ]);
    }
    
    /**
     * Create a new notification
     */
    public function store(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'user_type' => 'required|string|in:beneficiary,family_member,cose_staff,care_manager',
            'message_title' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Create the notification
        $notification = Notification::create([
            'user_id' => $validated['user_id'],
            'user_type' => $validated['user_type'],
            'message_title' => $validated['message_title'] ?? null,
            'message' => $validated['message'],
            'date_created' => Carbon::now(),
            'is_read' => false
        ]);
        
        return response()->json([
            'success' => true,
            'notification' => $notification,
            'message' => 'Notification created successfully'
        ]);
    }
    
    /**
     * Get notifications based on user role
     */
    private function getNotificationsByUserRole($user, $onlyUnread = false)
    {
        $query = Notification::query();
        
        if ($onlyUnread) {
            $query->where('is_read', false);
        }
        
        // Staff members check
        if ($user->role_id <= 3) { // Admin, Care Manager, or Care Worker
            $query->where('user_type', 'cose_staff')
                  ->where('user_id', $user->id);
        } 
        // For portal users (future implementation)
        // We would need to check session data or API token to determine
        // if they are beneficiary or family member
        
        return $query->orderBy('date_created', 'desc')->get();
    }
    
    /**
     * Determine the correct user type for notifications based on user role
     * 
     * @param User $user
     * @param string|null $routeUserType
     * @return string
     */
    private function determineUserType($user, $routeUserType = null)
    {
        // If route specifies user type, use that (for API flexibility)
        if ($routeUserType) {
            return $routeUserType;
        }
        
        // For staff users (roles 1-3), they all use 'cose_staff' user_type in notifications
        // This includes admin (1), care_manager (2), and care_worker (3)
        if ($user->role_id >= 1 && $user->role_id <= 3) {
            return 'cose_staff';
        }
        
        // For portal users, we would need to determine if they're a beneficiary or family member
        // This typically requires checking which portal they logged in through
        // or examining session data to know their user type
        
        // Default fallback - this should generally not be reached
        // since users should always have a valid type
        return 'cose_staff';
    }

    
}