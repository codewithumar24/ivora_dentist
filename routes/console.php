<?php

use App\Console\Commands\MarkMissedAppointments;
use App\Console\Commands\PurgeExpiredTokens;
use App\Console\Commands\SendAppointmentReminders;
use App\Console\Commands\SendDailySchedule;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled work
|--------------------------------------------------------------------------
| One cron entry on the server drives all of this:
|
|   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
*/

// Reminder email + SMS for anything happening inside the reminder window.
Schedule::command(SendAppointmentReminders::class)
    ->hourly()
    ->withoutOverlapping()
    ->onSuccess(fn () => logger('Appointment reminders dispatched'));

// Each practitioner's chair list, first thing in the morning.
Schedule::command(SendDailySchedule::class)->dailyAt('07:00');

// Yesterday's unattended visits become no-shows so the reports stay honest.
Schedule::command(MarkMissedAppointments::class)->dailyAt('23:45');

// Housekeeping.
Schedule::command(PurgeExpiredTokens::class)->daily();
Schedule::command('queue:prune-failed --hours=168')->weekly();

// If you run without a queue worker, uncomment this to flush the queue on cron:
// Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();
