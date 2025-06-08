<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Services\LogService;

class NotificationsApiController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * List notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Determine user type and correct ID for notifications
        if ($user->role_id == 4) { // Beneficiary
            $notifications = Notification::where('user_id', $user->beneficiary_id)
                ->where('user_type', 'beneficiary')
                ->orderByDesc('created_at')
                ->paginate(20);
        } elseif ($user->role_id == 5) { // Family Member
            $notifications = Notification::where('user_id', $user->family_member_id)
                ->where('user_type', 'family_member')
                ->orderByDesc('created_at')
                ->paginate(20);
        } else {
            // Default: show notifications for staff user_id
            $notifications = Notification::where('user_id', $user->id)
                ->where('user_type', 'staff')
                ->orderByDesc('created_at')
                ->paginate(20);
        }

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role_id == 4) { // Beneficiary
            $notification = Notification::where('user_id', $user->beneficiary_id)
                ->where('user_type', 'beneficiary')
                ->where('notification_id', $id)
                ->first();
        } elseif ($user->role_id == 5) { // Family Member
            $notification = Notification::where('user_id', $user->family_member_id)
                ->where('user_type', 'family_member')
                ->where('notification_id', $id)
                ->first();
        } else { // Staff
            $notification = Notification::where('user_id', $user->id)
                ->where('user_type', 'cose_staff')
                ->where('notification_id', $id)
                ->first();
        }

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        $notification->is_read = true;
        $notification->updated_at = now();
        $notification->save();

        // Log the action
        $this->logService->createLog(
            'notification',
            $notification->notification_id,
            'notification_marked_as_read',
            "Notification ID {$notification->notification_id} marked as read by user {$user->id}",
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.'
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $updatedCount = 0;

        if ($user->role_id == 4) { // Beneficiary
            $updatedCount = Notification::where('user_id', $user->beneficiary_id)
                ->where('user_type', 'beneficiary')
                ->where('is_read', false)
                ->update(['is_read' => true, 'updated_at' => now()]);
        } elseif ($user->role_id == 5) { // Family Member
            $updatedCount = Notification::where('user_id', $user->family_member_id)
                ->where('user_type', 'family_member')
                ->where('is_read', false)
                ->update(['is_read' => true, 'updated_at' => now()]);
        } else { // Staff
            $updatedCount = Notification::where('user_id', $user->id)
                ->where('user_type', 'cose_staff')
                ->where('is_read', false)
                ->update(['is_read' => true, 'updated_at' => now()]);
        }

        // Log the action
        $this->logService->createLog(
            'notification',
            null,
            'notifications_marked_all_as_read',
            "All notifications marked as read by user {$user->id}",
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
            'updated_count' => $updatedCount
        ]);
    }
}
