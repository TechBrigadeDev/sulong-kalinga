<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GenerateVisitationOccurrences::class,
        Commands\SendAppointmentReminders::class, // Add the new command here
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Generate visitation occurrences monthly to ensure upcoming appointments are available
        $schedule->command('visitations:generate-occurrences --months=6')
                 ->monthly()
                 ->description('Generate upcoming visitation occurrences')
                 ->emailOutputOnFailure(env('ADMIN_EMAIL'));
        
        // Run the appointment reminders command hourly
        // This ensures both 6am notifications for flexible appointments
        // and 3-hour-before notifications for timed appointments
        $schedule->command('appointments:send-reminders')
                 ->hourly()
                 ->description('Send reminder notifications for upcoming appointments')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/appointment-reminders.log'));
                 
        // Weekly cleanup of old occurrences (optional - keeps database size manageable)
        $schedule->command('visitations:cleanup-old-occurrences --months=12')
                 ->weekly()
                 ->saturdays()
                 ->at('01:00')
                 ->description('Clean up old visitation occurrences');
                 
        // Daily check for changed statuses (e.g., mark past appointments as completed)
        $schedule->command('visitations:update-statuses')
                 ->dailyAt('00:05')
                 ->description('Update visitation statuses based on dates');
                 
        // Database maintenance - run every Sunday 
        $schedule->command('db:optimize')
                 ->weekly()
                 ->sundays()
                 ->at('02:00')
                 ->description('Optimize database tables');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}