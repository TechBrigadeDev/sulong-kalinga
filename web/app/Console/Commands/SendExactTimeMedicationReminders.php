<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MedicationSchedule;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Carbon\Carbon;
use App\Services\NotificationService;

class SendExactTimeMedicationReminders extends Command
{
    protected $signature = 'medications:send-exact-reminders';
    protected $description = 'Send medication reminders at the exact scheduled time';

    protected $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->notificationService = app(NotificationService::class);
    }

    public function handle()
    {
        $now = Carbon::now();
        $windowMinutes = 5; // Send reminders for meds scheduled within ±5 minutes of now

        $timeFields = [
            'morning_time' => 'morning',
            'noon_time' => 'noon',
            'evening_time' => 'evening',
            'night_time' => 'night',
        ];

        $count = 0;

        foreach ($timeFields as $field => $label) {
            $medications = MedicationSchedule::with('beneficiary')
                ->where('status', 'active')
                ->where(function($q) use ($now) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $now->format('Y-m-d'));
                })
                ->where('start_date', '<=', $now->format('Y-m-d'))
                ->whereNotNull($field)
                ->get();

            foreach ($medications as $medication) {
                $scheduled = Carbon::parse($medication->$field);
                // Compare only hour and minute
                if (abs($now->diffInMinutes($scheduled)) <= $windowMinutes) {
                    $this->sendMedicationReminder($medication, $label, $scheduled->format('g:i A'));
                    $count++;
                }
            }
        }

        $this->info("Sent {$count} medication reminders at " . $now->format('Y-m-d H:i'));
        return 0;
    }

    private function sendMedicationReminder($medication, $timeOfDay, $timeStr)
    {
        $beneficiary = $medication->beneficiary;
        if (!$beneficiary) return;

        $withFood = '';
        switch ($timeOfDay) {
            case 'morning': $withFood = $medication->with_food_morning ? ' with food' : ''; break;
            case 'noon': $withFood = $medication->with_food_noon ? ' with food' : ''; break;
            case 'evening': $withFood = $medication->with_food_evening ? ' with food' : ''; break;
            case 'night': $withFood = $medication->with_food_night ? ' with food' : ''; break;
        }

        $title = "Medication Reminder";
        $message = "⏰ MEDICATION REMINDER\n\n";
        $message .= "It's time for {$beneficiary->first_name} to take:\n";
        $message .= "• {$medication->medication_name} ({$medication->dosage})\n";
        $message .= "• Time: {$timeStr} {$withFood}\n";
        if ($medication->special_instructions) {
            $message .= "• Special instructions: {$medication->special_instructions}\n";
        }

        // Notify beneficiary
        $this->notificationService->notifyBeneficiary($beneficiary->beneficiary_id, $title, $message);

        // Notify family members
        $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)->get();
        foreach ($familyMembers as $familyMember) {
            $this->notificationService->notifyFamilyMember($familyMember->family_member_id, $title, $message);
        }

        // Notify assigned care worker
        if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id) {
            $this->notificationService->notifyStaff(
                $beneficiary->generalCarePlan->care_worker_id,
                $title,
                $message
            );
        }
    }
}