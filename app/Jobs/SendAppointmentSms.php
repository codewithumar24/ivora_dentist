<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAppointmentSms implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public Appointment $appointment, public string $kind = 'confirmation') {}

    public function handle(SmsService $sms): void
    {
        $appointment = $this->appointment->loadMissing('patient', 'doctor.user');
        $phone = $appointment->patient->phone;

        if (! $phone) {
            return;
        }

        $when = $appointment->starts_at_carbon->format('D j M, g:i A');
        $doctor = $appointment->doctor->full_name;

        $body = match ($this->kind) {
            'reminder' => "Ivora Dental: reminder — {$when} with {$doctor}. Reply CANCEL to release the slot. Ref {$appointment->reference}",
            'reschedule' => "Ivora Dental: your visit moved to {$when} with {$doctor}. Ref {$appointment->reference}",
            'status' => "Ivora Dental: {$appointment->reference} is now {$appointment->status_label}. {$when}",
            default => "Ivora Dental: we received your request for {$when} with {$doctor}. We'll confirm shortly. Ref {$appointment->reference}",
        };

        $sms->send($phone, $body, $this->kind, $appointment->patient);
    }
}
