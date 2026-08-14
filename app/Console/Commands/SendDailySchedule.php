<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\SmsService;
use Illuminate\Console\Command;

/** 7am digest: each dentist gets their own list for the day. */
class SendDailySchedule extends Command
{
    protected $signature = 'ivora:daily-schedule';

    protected $description = 'Send each practitioner their list of patients for today';

    public function handle(SmsService $sms): int
    {
        foreach (Doctor::with('user')->active()->get() as $doctor) {
            $count = Appointment::where('doctor_id', $doctor->id)->today()
                ->whereNot('status', Appointment::CANCELLED)->count();

            if ($count === 0 || ! $doctor->user->phone) {
                continue;
            }

            $first = Appointment::where('doctor_id', $doctor->id)->today()
                ->orderBy('starts_at')->value('starts_at');

            $sms->send(
                $doctor->user->phone,
                "Ivora: {$count} patient(s) today, first chair at ".date('g:i A', strtotime($first)).'.',
                'digest',
                $doctor->user
            );

            $this->line("→ {$doctor->full_name}: {$count} appointment(s)");
        }

        return self::SUCCESS;
    }
}
