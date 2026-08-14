<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;

class MarkMissedAppointments extends Command
{
    protected $signature = 'ivora:mark-no-shows';

    protected $description = 'Flag yesterday\'s uncompleted appointments as no-shows';

    public function handle(): int
    {
        $count = Appointment::query()
            ->whereIn('status', [Appointment::PENDING, Appointment::CONFIRMED])
            ->whereDate('scheduled_on', '<', today())
            ->update(['status' => Appointment::NO_SHOW]);

        $this->info("Marked {$count} appointment(s) as no-show.");

        return self::SUCCESS;
    }
}
