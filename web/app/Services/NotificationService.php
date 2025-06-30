<?php
// filepath: /Users/Shared/jjspscl/projects/sulong-kalinga/web/app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\FcmToken;
use Carbon\Carbon;
use App\Notifications\ExpoPushNotification;

class NotificationService
{
    /**
     * Send a notification to a COSE staff member
     *
     * @param int $userId
     * @param string $title
     * @param string $message
     * @return Notification
     */
    public function notifyStaff($userId, $title, $message)
    {
        $notification = $this->createNotification($userId, 'cose_staff', $title, $message);
        
        // Send push notification if user has FCM token
        $user = User::find($userId);
        if ($user) {
            $this->sendExpoPush($user, $title, $message, 'cose_staff');
        }
        
        return $notification;
    }
    
    /**
     * Send a notification to a beneficiary
     *
     * @param int $beneficiaryId
     * @param string $title
     * @param string $message
     * @return Notification
     */
    public function notifyBeneficiary($beneficiaryId, $title, $message)
    {
        $notification = $this->createNotification($beneficiaryId, 'beneficiary', $title, $message);
        
        // Send push notification if beneficiary has FCM token
        $beneficiary = Beneficiary::find($beneficiaryId);
        if ($beneficiary) {
            $this->sendExpoPush($beneficiary, $title, $message, 'beneficiary');
        }
        
        return $notification;
    }
    
    /**
     * Send a notification to a family member
     *
     * @param int $familyMemberId
     * @param string $title
     * @param string $message
     * @return Notification
     */
    public function notifyFamilyMember($familyMemberId, $title, $message)
    {
        $notification = $this->createNotification($familyMemberId, 'family_member', $title, $message);
        
        // Send push notification if family member has FCM token
        $familyMember = FamilyMember::find($familyMemberId);
        if ($familyMember) {
            $this->sendExpoPush($familyMember, $title, $message, 'family_member');
        }
        
        return $notification;
    }
    
    /**
     * Send notifications to all care workers
     *
     * @param string $title
     * @param string $message
     * @return array
     */
    public function notifyAllCareWorkers($title, $message)
    {
        $careWorkers = User::where('role_id', 3)->get();
        $notifications = [];
        
        foreach ($careWorkers as $worker) {
            $notifications[] = $this->notifyStaff($worker->id, $title, $message);
        }
        
        return $notifications;
    }
    
    /**
     * Send notifications to all care managers
     *
     * @param string $title
     * @param string $message
     * @return array
     */
    public function notifyAllCareManagers($title, $message)
    {
        $careManagers = User::where('role_id', 2)->get();
        $notifications = [];

        foreach ($careManagers as $manager) {
            $notifications[] = $this->notifyStaff($manager->id, $title, $message);
        }

        return $notifications;
    }

    /**
     * Register FCM token for a user
     *
     * @param int $userId
     * @param string $role
     * @param string $token
     * @return FcmToken
     */
    public function register($userId, $role, $token)
    {
        // Validate Expo push token format
        if (!$this->isValidExpoPushToken($token)) {
            throw new \InvalidArgumentException('Invalid Expo push token format');
        }

        return FcmToken::registerToken($userId, $role, $token);
    }

    /**
     * Get FCM token by user ID and role
     *
     * @param int $userId
     * @param string $role
     * @return FcmToken|null
     */
    public function getTokenByUser($userId, $role)
    {
        return FcmToken::getTokenByUser($userId, $role);
    }

    /**
     * Send push notifications to multiple users by their IDs and roles
     *
     * @param array $users Array of ['user_id' => id, 'role' => role] 
     * @param string $title
     * @param string $message
     * @return array
     */
    public function sendByIds(array $users, $title, $message)
    {
        $results = [];
        
        foreach ($users as $userData) {
            $userId = $userData['user_id'];
            $role = $userData['role'];
            
            try {
                // Get user model based on role
                $user = null;
                switch ($role) {
                    case 'cose_staff':
                        $user = User::find($userId);
                        break;
                    case 'beneficiary':
                        $user = Beneficiary::find($userId);
                        break;
                    case 'family_member':
                        $user = FamilyMember::find($userId);
                        break;
                }
                
                if ($user) {
                    $this->sendExpoPush($user, $title, $message, $role);
                    $results[] = ['user_id' => $userId, 'role' => $role, 'success' => true, 'message' => 'Notification sent'];
                } else {
                    $results[] = ['user_id' => $userId, 'role' => $role, 'success' => false, 'message' => 'User not found'];
                }
            } catch (\Exception $e) {
                $results[] = ['user_id' => $userId, 'role' => $role, 'success' => false, 'message' => $e->getMessage()];
            }
        }
        
        return $results;
    }
    
    /**
     * Create a notification record
     *
     * @param int $userId
     * @param string $userType
     * @param string $title
     * @param string $message
     * @return Notification
     */
    private function createNotification($userId, $userType, $title, $message)
    {
        return Notification::create([
            'user_id' => $userId,
            'user_type' => $userType,
            'message_title' => $title,
            'message' => $message,
            'date_created' => Carbon::now(),
            'is_read' => false
        ]);
    }

    /**
     * Send Expo push notification using the dedicated notification class
     *
     * @param mixed $notifiable
     * @param string $title
     * @param string $message
     * @param string $role
     * @return void
     */
    private function sendExpoPush($notifiable, string $title, string $message, string $role): void
    {
        $fcmToken = null;
        $userId = null;

        \Log::info('=== PUSH NOTIFICATION DEBUG START ===');
        \Log::info('Notifiable details', [
            'class' => get_class($notifiable),
            'id' => $notifiable->id ?? 'no id',
            'role' => $role
        ]);
        
        if ($notifiable instanceof User && $role === 'cose_staff') {
            $userId = $notifiable->id;
        } elseif ($notifiable instanceof Beneficiary && $role === 'beneficiary') {
            $userId = $notifiable->beneficiary_id;
        } elseif ($notifiable instanceof FamilyMember && $role === 'family_member') {
            $userId = $notifiable->family_member_id;
        }
        
        \Log::info('User ID resolved', ['user_id' => $userId]);
        
        if ($userId) {
            $fcmToken = FcmToken::getTokenByUser($userId, $role);
            \Log::info('Token lookup result', [
                'token_found' => $fcmToken ? 'YES' : 'NO',
                'token_value' => $fcmToken ? $fcmToken->token : 'NONE'
            ]);
        }
        
        // Only send if FCM token exists
        if ($fcmToken) {
            try {
                \Log::info('Attempting to send push notification');
                \Illuminate\Support\Facades\Notification::send(
                    $notifiable,
                    new ExpoPushNotification($title, $message)
                    // new ExpoPushNotification($title, $message, $fcmToken->token)
                );
                \Log::info('✅ Push notification sent successfully');
            } catch (\Exception $e) {
                \Log::error('❌ Failed to send push notification: ' . $e->getMessage());
                \Log::error('Exception trace: ' . $e->getTraceAsString());
            }
        } else {
            \Log::warning('❌ No FCM token found - push notification skipped');
        }
        
        \Log::info('=== PUSH NOTIFICATION DEBUG END ===');
    }

    /**
     * Validate Expo push token format
     *
     * @param string $token
     * @return bool
     */
    private function isValidExpoPushToken(string $token): bool
    {
        // Expo push tokens typically start with "ExponentPushToken[" or "ExpoPushToken["
        // and have a specific format
        $pattern = '/^(ExponentPushToken|ExpoPushToken)\[[\w-]+\]$/';
        return preg_match($pattern, $token) === 1;
    }
}