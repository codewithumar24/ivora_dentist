<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentSms;
use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Runs hourly from the scheduler. Picks up every confirmed or pending visit
 * that starts inside the reminder window and has not been reminded yet.
 */
class SendAppointmentReminders extends Command
{
    protected $signature = 'ivora:send-reminders {--hours= : Override the reminder window}';

    protected $description = 'Email + SMS reminders for appointments happening soon';

    public function handle(): int
    {
        $hours = (int) ($this->option('hours') ?: config('clinic.reminder_hours'));
        $windowEnd = now()->addHours($hours);

        $appointments = Appointment::query()
            ->with(['patient', 'doctor.user', 'service'])
            ->whereIn('status', [Appointment::PENDING, Appointment::CONFIRMED])
            ->whereNull('reminder_sent_at')
            ->whereBetween('scheduled_on', [now()->toDateString(), $windowEnd->toDateString()])
            ->get()
            ->filter(fn ($a) => $a->starts_at_carbon->between(now(), $windowEnd));

        if ($appointments->isEmpty()) {
            $this->info('Nothing to remind about.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($appointments->count());

        foreach ($appointments as $appointment) {
            Mail::to($appointment->patient->email)->queue(new AppointmentReminder($appointment));
            SendAppointmentSms::dispatch($appointment, 'reminder');
            $appointment->forceFill(['reminder_sent_at' => now()])->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Queued {$appointments->count()} reminder(s).");

        return self::SUCCESS;
    }
}
