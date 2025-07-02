<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MedicationSchedule;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class SendMedicationReminders extends Command
{
    protected $signature = 'medications:send-reminders {time_of_day? : morning, noon, evening, or night}';
    protected $description = 'Send medication reminders based on schedule times';

    protected $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->notificationService = app(NotificationService::class);
    }

    public function handle()
    {
        $timeOfDay = $this->argument('time_of_day');
        $now = Carbon::now();

        // If no specific time is provided, determine based on current time
        if (!$timeOfDay) {
            $hour = (int)$now->format('H');
            
            if ($hour >= 5 && $hour < 10) {
                $timeOfDay = 'morning';
            } elseif ($hour >= 10 && $hour < 14) {
                $timeOfDay = 'noon';
            } elseif ($hour >= 14 && $hour < 18) {
                $timeOfDay = 'evening';
            } elseif ($hour >= 18 || $hour < 5) {
                $timeOfDay = 'night';
            }
        }

        $this->info("Sending {$timeOfDay} medication reminders at " . $now->format('Y-m-d H:i:s'));

        // Find active medications for the current time of day
        $query = MedicationSchedule::with(['beneficiary'])
            ->where('status', 'active')
            ->where(function($q) use ($now) {
                // Include medications with no end_date or end_date >= today
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now->format('Y-m-d'));
            })
            ->where('start_date', '<=', $now->format('Y-m-d'));

        // Filter by time of day
        switch ($timeOfDay) {
            case 'morning':
                $query->whereNotNull('morning_time');
                break;
            case 'noon':
                $query->whereNotNull('noon_time');
                break;
            case 'evening':
                $query->whereNotNull('evening_time');
                break;
            case 'night':
                $query->whereNotNull('night_time');
                break;
            default:
                $this->error("Invalid time of day: {$timeOfDay}. Use morning, noon, evening, or night.");
                return 1;
        }

        $medications = $query->get();
        $count = 0;

        foreach ($medications as $medication) {
            try {
                if ($this->sendMedicationReminder($medication, $timeOfDay)) {
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send medication reminder: " . $e->getMessage(), [
                    'medication_id' => $medication->medication_schedule_id,
                    'beneficiary_id' => $medication->beneficiary_id
                ]);
                $this->error("Error sending reminder for medication ID {$medication->medication_schedule_id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully sent {$count} medication reminders for {$timeOfDay}");
        return 0;
    }

    private function sendMedicationReminder($medication, $timeOfDay)
    {
        // Get the beneficiary
        $beneficiary = $medication->beneficiary;
        if (!$beneficiary) {
            $this->warn("Skipping medication ID {$medication->medication_schedule_id}: Beneficiary not found");
            return false;
        }

        // Format the time and with_food flag
        $timeStr = '';
        $withFood = '';
        switch ($timeOfDay) {
            case 'morning':
                $timeStr = Carbon::parse($medication->morning_time)->format('g:i A');
                $withFood = $medication->with_food_morning ? ' with food' : '';
                break;
            case 'noon':
                $timeStr = Carbon::parse($medication->noon_time)->format('g:i A');
                $withFood = $medication->with_food_noon ? ' with food' : '';
                break;
            case 'evening':
                $timeStr = Carbon::parse($medication->evening_time)->format('g:i A');
                $withFood = $medication->with_food_evening ? ' with food' : '';
                break;
            case 'night':
                $timeStr = Carbon::parse($medication->night_time)->format('g:i A');
                $withFood = $medication->with_food_night ? ' with food' : '';
                break;
        }

        // Create notification message
        $title = "Medication Reminder";
        $message = "⏰ MEDICATION REMINDER\n\n";
        $message .= "It's time for {$beneficiary->first_name} to take:\n";
        $message .= "• {$medication->medication_name} ({$medication->dosage})\n";
        $message .= "• Time: {$timeStr} {$withFood}\n";

        if ($medication->special_instructions) {
            $message .= "• Special instructions: {$medication->special_instructions}\n";
        }
        
        // 1. Notify beneficiary
        $this->notificationService->notifyBeneficiary($beneficiary->beneficiary_id, $title, $message);

        // 2. Notify family members
        $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)->get();
        foreach ($familyMembers as $familyMember) {
            $this->notificationService->notifyFamilyMember($familyMember->family_member_id, $title, $message);
        }

        // 3. Notify assigned care worker
        if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id) {
            $this->notificationService->notifyStaff(
                $beneficiary->generalCarePlan->care_worker_id,
                $title,
                $message
            );
        }

        return true;
    }
}