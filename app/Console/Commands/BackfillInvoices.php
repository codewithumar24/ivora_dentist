<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Console\Command;

class BackfillInvoices extends Command
{
    protected $signature = 'ivora:backfill-invoices';

    protected $description = 'Raise invoices for completed appointments that never got one';

    public function handle(AppointmentService $service): int
    {
        $appointments = Appointment::with('service')
            ->where('status', Appointment::COMPLETED)
            ->whereDoesntHave('invoice')->get();

        foreach ($appointments as $appointment) {
            $service->ensureInvoice($appointment);
        }

        $this->info("Created {$appointments->count()} invoice(s).");

        return self::SUCCESS;
    }
}
