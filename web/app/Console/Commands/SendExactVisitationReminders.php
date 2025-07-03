<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VisitationOccurrence;
use App\Models\Visitation;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Carbon\Carbon;
use App\Services\NotificationService;

class SendExactVisitationReminders extends Command
{
    protected $signature = 'visitations:send-exact-reminders';
    protected $description = 'Send reminders for visitations at their exact scheduled reminder time';

    protected $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->notificationService = app(NotificationService::class);
    }

    public function handle()
    {
        $now = Carbon::now();
        // Calculate target time range (5 minutes from now)
        $targetTime = $now->copy()->addMinutes(5);
        $targetHour = $targetTime->format('H');
        $targetMinute = $targetTime->format('i');
        
        $this->info("Checking for visitations scheduled at approximately " . $targetTime->format('Y-m-d H:i'));

        // Fetch visitation occurrences that start in 5 minutes
        $occurrences = VisitationOccurrence::with('visitation')
            ->whereHas('visitation', function($q) use ($targetHour, $targetMinute) {
                $q->whereRaw("EXTRACT(HOUR FROM start_time) = ?", [$targetHour])
                ->whereRaw("EXTRACT(MINUTE FROM start_time) = ?", [$targetMinute]);
            })
            ->where('occurrence_date', $now->toDateString())
            ->where('status', 'scheduled')
            ->get();

        $count = 0;
        foreach ($occurrences as $occurrence) {
            $this->sendReminder($occurrence);
            $count++;
        }

        $this->info("Sent {$count} visitation reminders at {$now->format('Y-m-d H:i')}");
        return 0;
    }

    private function sendReminder(VisitationOccurrence $occurrence)
    {
        $visitation = Visitation::find($occurrence->visitation_id);
        if (!$visitation) {
            return;
        }

        $beneficiary = Beneficiary::find($visitation->beneficiary_id);
        $careWorker = User::find($visitation->care_worker_id);

        $dateFormatted = Carbon::parse($occurrence->occurrence_date)->format('l, F j, Y');
        $timeInfo = $visitation->is_flexible_time
            ? 'flexible time (schedule to be determined)'
            : 'from ' . Carbon::parse($visitation->start_time)->format('g:i A') . ' to ' .
              Carbon::parse($visitation->end_time)->format('g:i A');

        $visitType = ucwords(str_replace('_', ' ', $visitation->visit_type));

        $title = "Visitation Reminder";
        $message = "Reminder: You have a visitation scheduled for {$dateFormatted} at {$timeInfo}. " .
                   "Visit type: {$visitType}.";

        // Notify care worker
        if ($careWorker) {
            $this->notificationService->notifyStaff($careWorker->id, $title, $message);
        }

        // Notify beneficiary
        if ($beneficiary) {
            $this->notificationService->notifyBeneficiary($beneficiary->beneficiary_id, $title, $message);
        }

        // Notify family members
        $familyMembers = FamilyMember::where('related_beneficiary_id', $visitation->beneficiary_id)->get();
        foreach ($familyMembers as $familyMember) {
            $this->notificationService->notifyFamilyMember($familyMember->family_member_id, $title, $message);
        }
    }
}