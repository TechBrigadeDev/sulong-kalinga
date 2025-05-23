<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\InternalAppointmentsController;

class SendInternalAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-internal-reminders';
    protected $description = 'Send reminders for upcoming internal appointments';

    public function handle()
    {
        $controller = new InternalAppointmentsController();
        $count = $controller->sendAppointmentReminders();
        
        $this->info("Sent {$count} internal appointment reminders");
        return 0;
    }
}