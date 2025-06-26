<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('visitations:generate-occurrences --months=6')
    ->monthly()
    ->description('Generate upcoming visitation occurrences')
    ->emailOutputOnFailure(env('ADMIN_EMAIL'));

Schedule::command('appointments:send-internal-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping();

Schedule::command('appointments:send-reminders')
    ->hourly()
    ->description('Send reminder notifications for upcoming appointments')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/appointment-reminders.log'));

Schedule::command('visitations:cleanup-old-occurrences --months=12')
    ->weekly()
    ->saturdays()
    ->at('01:00')
    ->description('Clean up old visitation occurrences');

Schedule::command('visitations:update-statuses')
    ->dailyAt('00:05')
    ->description('Update visitation statuses based on dates');

Schedule::command('db:optimize')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->description('Optimize database tables');

Schedule::command('medications:send-reminders morning')->dailyAt('07:00');
Schedule::command('medications:send-reminders noon')->dailyAt('11:30');
Schedule::command('medications:send-reminders evening')->dailyAt('17:00');
Schedule::command('medications:send-reminders night')->dailyAt('20:00');
Schedule::command('medications:send-exact-reminders')->everyMinute();
