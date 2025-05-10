<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitation;
use App\Models\VisitationOccurrence;
use App\Models\Notification;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send reminder notifications for upcoming appointments';

    public function handle()
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $this->info('Starting appointment reminder notifications at ' . $now->format('Y-m-d H:i:s'));

        // Step 1: Handle flexible time appointments scheduled for today (send at 6am)
        if ($now->hour === 6 && $now->minute < 15) { // Run between 6:00-6:15am
            $this->info('Processing flexible time appointments for today');
            $this->sendFlexibleTimeReminders($today);
        }

        // Step 2: Handle timed appointments occurring in the next 3 hours
        $this->info('Processing timed appointments occurring in the next 3 hours');
        $this->sendTimedAppointmentReminders($now);

        $this->info('Finished processing appointment reminders');
        return 0;
    }

    /**
     * Send reminders for flexible time appointments scheduled for today
     */
    private function sendFlexibleTimeReminders($today)
    {
        // Find today's flexible time appointments from occurrences
        $flexibleOccurrences = VisitationOccurrence::with(['visitation.beneficiary', 'visitation.careWorker'])
            ->whereHas('visitation', function($query) {
                $query->where('is_flexible_time', true);
            })
            ->where('occurrence_date', $today->format('Y-m-d'))
            ->where('status', 'scheduled')
            ->get();
            
        $this->info("Found {$flexibleOccurrences->count()} flexible time occurrences for today");

        foreach ($flexibleOccurrences as $occurrence) {
            $this->sendReminderNotifications($occurrence->visitation, $occurrence->occurrence_date);
        }

        // Also check directly in visitations table for non-recurring flexible time appointments
        $flexibleVisitations = Visitation::with(['beneficiary', 'careWorker'])
            ->where('is_flexible_time', true)
            ->where('visitation_date', $today->format('Y-m-d'))
            ->where('status', 'scheduled')
            ->whereDoesntHave('occurrences')
            ->get();

        $this->info("Found {$flexibleVisitations->count()} flexible time visitations for today");
            
        foreach ($flexibleVisitations as $visitation) {
            $this->sendReminderNotifications($visitation, $visitation->visitation_date);
        }
    }

    /**
     * Send reminders for timed appointments occurring in the next 3 hours
     * PostgreSQL compatible version
     */
    private function sendTimedAppointmentReminders($now)
    {
        // Calculate the time window (now to 3 hours from now)
        $threeHoursFromNow = $now->copy()->addHours(3);
        
        // Only process appointments that start in 2.75 to 3.25 hours from now
        // This creates a 30-minute window for the scheduler to catch appointments
        $minTime = $now->copy()->addHours(2.75)->format('H:i:s');
        $maxTime = $now->copy()->addHours(3.25)->format('H:i:s');
        $today = $now->format('Y-m-d');
        
        $this->info("Looking for appointments between $minTime and $maxTime");

        try {
            // Find timed occurrences - PostgreSQL compatible query
            $timedOccurrences = VisitationOccurrence::with(['visitation.beneficiary', 'visitation.careWorker'])
                ->whereHas('visitation', function($query) {
                    $query->where('is_flexible_time', false);
                })
                ->where('occurrence_date', $today)
                ->whereHas('visitation', function($query) use ($minTime, $maxTime) {
                    // PostgreSQL compatible time extraction
                    $query->whereRaw("CAST(start_time AS time) BETWEEN ? AND ?", [$minTime, $maxTime]);
                })
                ->where('status', 'scheduled')
                ->get();

            $this->info("Found {$timedOccurrences->count()} timed occurrences starting in approximately 3 hours");

            foreach ($timedOccurrences as $occurrence) {
                $this->sendReminderNotifications($occurrence->visitation, $occurrence->occurrence_date);
            }

            // Also check directly in visitations table for non-recurring timed appointments
            $timedVisitations = Visitation::with(['beneficiary', 'careWorker'])
                ->where('is_flexible_time', false)
                ->where('visitation_date', $today)
                ->whereRaw("CAST(start_time AS time) BETWEEN ? AND ?", [$minTime, $maxTime])
                ->where('status', 'scheduled')
                ->whereDoesntHave('occurrences')
                ->get();

            $this->info("Found {$timedVisitations->count()} timed visitations starting in approximately 3 hours");
                
            foreach ($timedVisitations as $visitation) {
                $this->sendReminderNotifications($visitation, $visitation->visitation_date);
            }
        } catch (\Exception $e) {
            $this->error("Error processing timed appointments: " . $e->getMessage());
            Log::error("Error in appointment reminders: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send reminder notifications for a specific visitation
     */
    private function sendReminderNotifications(Visitation $visitation, $occurrenceDate)
    {
        $beneficiary = Beneficiary::find($visitation->beneficiary_id);
        $careWorker = User::find($visitation->care_worker_id);
        
        // Skip if essential data is missing
        if (!$beneficiary || !$careWorker) {
            $this->warn("Skipping appointment #{$visitation->visitation_id} due to missing beneficiary or care worker data");
            return;
        }
        
        // Format date and time information
        $dateFormatted = Carbon::parse($occurrenceDate)->format('l, F j, Y');
        
        $timeInfo = $visitation->is_flexible_time ? 
            'flexible time (schedule to be determined)' : 
            'from ' . Carbon::parse($visitation->start_time)->format('g:i A') . ' to ' . 
            Carbon::parse($visitation->end_time)->format('g:i A');
        
        $visitType = ucwords(str_replace('_', ' ', $visitation->visit_type));
        
        // Create the message
        $title = "Upcoming Appointment Reminder";
        $message = "Reminder: You have an appointment scheduled for {$dateFormatted} at {$timeInfo}. " .
                  "Visit type: {$visitType}. " .
                  "This appointment is between {$beneficiary->first_name} {$beneficiary->last_name} " .
                  "and care worker {$careWorker->first_name} {$careWorker->last_name}.";
        
        // Get care manager of the care worker
        $careManager = null;
        if ($careWorker && $careWorker->assigned_care_manager_id) {
            $careManager = User::find($careWorker->assigned_care_manager_id);
        }
        
        // Get family members
        $familyMembers = FamilyMember::where('related_beneficiary_id', $visitation->beneficiary_id)->get();
        
        try {
            // Send notification to care worker
            Notification::create([
                'user_id' => $careWorker->id,
                'user_type' => 'cose_staff',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
            
            // Send notification to care manager (if exists)
            if ($careManager) {
                Notification::create([
                    'user_id' => $careManager->id,
                    'user_type' => 'cose_staff',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now(),
                    'is_read' => false
                ]);
            }
            
            // Notify beneficiary if they have portal access
            if ($beneficiary->portal_account_id) {
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
            foreach ($familyMembers as $familyMember) {
                if ($familyMember->portal_account_id) {
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
            
            $this->info("Sent reminder notifications for appointment between care worker {$careWorker->first_name} and beneficiary {$beneficiary->first_name} on {$dateFormatted}");
        } catch (\Exception $e) {
            $this->error("Failed to send notifications: " . $e->getMessage());
            Log::error("Failed to send appointment reminders: " . $e->getMessage());
        }
    }
}