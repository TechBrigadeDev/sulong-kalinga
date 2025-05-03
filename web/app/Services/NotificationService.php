<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Services\NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Carbon\Carbon;

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
        return $this->createNotification($userId, 'cose_staff', $title, $message);
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
        return $this->createNotification($beneficiaryId, 'beneficiary', $title, $message);
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
        return $this->createNotification($familyMemberId, 'family_member', $title, $message);
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
}