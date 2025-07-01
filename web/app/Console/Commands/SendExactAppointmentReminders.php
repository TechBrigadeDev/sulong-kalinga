<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Carbon\Carbon;

class SendExactAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-exact-reminders';
    protected $description = 'Send reminders for appointments at their exact scheduled reminder time';

    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d H:i:00'); // Current minute

        // Fetch appointments that need a reminder at this exact minute
        $appointments = Appointment::where('reminder_time', $now)->get();

        $count = 0;
        foreach ($appointments as $appointment) {
            $this->sendReminder($appointment);
            $count++;
        }

        $this->info("Sent {$count} exact appointment reminders at {$now}");
        return 0;
    }

    private function sendReminder(Appointment $appointment)
    {
        // Example: adjust as needed for your app's relationships
        $beneficiary = Beneficiary::find($appointment->beneficiary_id);
        $careWorker = User::find($appointment->care_worker_id);

        $dateFormatted = Carbon::parse($appointment->appointment_date)->format('l, F j, Y');
        $timeFormatted = Carbon::parse($appointment->appointment_time)->format('g:i A');

        $title = "Appointment Reminder";
        $message = "Reminder: You have an appointment scheduled for {$dateFormatted} at {$timeFormatted}.";

        // Notify care worker
        if ($careWorker) {
            Notification::create([
                'user_id' => $careWorker->id,
                'user_type' => 'cose_staff',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }

        // Notify beneficiary
        if ($beneficiary) {
            Notification::create([
                'user_id' => $beneficiary->beneficiary_id,
                'user_type' => 'beneficiary',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }

        // Notify family members
        $familyMembers = FamilyMember::where('related_beneficiary_id', $appointment->beneficiary_id)->get();
        foreach ($familyMembers as $familyMember) {
            Notification::create([
                'user_id' => $familyMember->family_member_id,
                'user_type' => 'family_member',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }
    }
}