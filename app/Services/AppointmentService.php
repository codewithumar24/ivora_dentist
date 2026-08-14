<?php

namespace App\Services;

use App\Jobs\SendAppointmentSms;
use App\Mail\AppointmentBooked;
use App\Mail\AppointmentStatusChanged;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use App\Notifications\AppointmentUpdated;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(protected SlotService $slots) {}

    public function book(User $patient, array $data, string $source = 'online'): Appointment
    {
        $doctor = Doctor::with('user')->findOrFail($data['doctor_id']);
        $date = Carbon::parse($data['scheduled_on']);
        $service = isset($data['service_id']) ? Service::find($data['service_id']) : null;

        if (! $this->slots->isSlotFree($doctor, $date, $data['starts_at'])) {
            throw ValidationException::withMessages([
                'starts_at' => 'That time was taken while you were filling in the form. Pick another slot.',
            ]);
        }

        $length = $service?->duration_minutes ?: config('clinic.slot_minutes');

        $appointment = DB::transaction(function () use ($patient, $doctor, $service, $date, $data, $length, $source) {
            return Appointment::create([
                'reference' => Appointment::nextReference(),
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'service_id' => $service?->id,
                'scheduled_on' => $date->toDateString(),
                'starts_at' => $data['starts_at'],
                'ends_at' => Carbon::parse($data['starts_at'])->addMinutes($length)->format('H:i'),
                'status' => Appointment::PENDING,
                'source' => $source,
                'visit_type' => $data['visit_type'] ?? 'first_visit',
                'reason' => $data['reason'] ?? null,
                'symptoms' => $data['symptoms'] ?? null,
                'fee' => $service?->price_from ?: $doctor->consultation_fee,
                'created_by' => auth()->id(),
            ]);
        });

        $appointment->load('doctor.user', 'service', 'patient');

        Mail::to($patient->email)->queue(new AppointmentBooked($appointment));
        SendAppointmentSms::dispatch($appointment, 'confirmation');
        $patient->notify(new AppointmentUpdated($appointment, 'Appointment requested'));
        ActivityLog::record('created', "Booked appointment {$appointment->reference}", $appointment);

        return $appointment;
    }

    public function changeStatus(Appointment $appointment, string $status, ?string $reason = null): Appointment
    {
        $appointment->status = $status;

        match ($status) {
            Appointment::CONFIRMED => $appointment->confirmed_at = now(),
            Appointment::COMPLETED => $appointment->completed_at = now(),
            Appointment::CANCELLED => tap($appointment, function ($a) use ($reason) {
                $a->cancelled_at = now();
                $a->cancelled_by = auth()->id();
                $a->cancel_reason = $reason;
            }),
            default => null,
        };

        $appointment->save();

        if ($status === Appointment::COMPLETED) {
            $this->ensureInvoice($appointment);
        }

        $appointment->loadMissing('patient', 'doctor.user', 'service');

        Mail::to($appointment->patient->email)->queue(new AppointmentStatusChanged($appointment));
        SendAppointmentSms::dispatch($appointment, 'status');
        $appointment->patient->notify(new AppointmentUpdated($appointment, "Appointment {$appointment->status_label}"));
        ActivityLog::record('updated', "Set {$appointment->reference} to {$status}", $appointment);

        return $appointment;
    }

    public function reschedule(Appointment $appointment, string $date, string $time): Appointment
    {
        $target = Carbon::parse($date);

        if (! $this->slots->isSlotFree($appointment->doctor, $target, $time, $appointment->id)) {
            throw ValidationException::withMessages(['starts_at' => 'That slot is no longer open.']);
        }

        $length = $appointment->service?->duration_minutes ?: config('clinic.slot_minutes');

        $appointment->update([
            'scheduled_on' => $target->toDateString(),
            'starts_at' => $time,
            'ends_at' => Carbon::parse($time)->addMinutes($length)->format('H:i'),
            'status' => Appointment::PENDING,
            'confirmed_at' => null,
            'reminder_sent_at' => null,
        ]);

        Mail::to($appointment->patient->email)->queue(new AppointmentStatusChanged($appointment, 'rescheduled'));
        SendAppointmentSms::dispatch($appointment, 'reschedule');
        ActivityLog::record('updated', "Rescheduled {$appointment->reference}", $appointment);

        return $appointment;
    }

    public function ensureInvoice(Appointment $appointment): Invoice
    {
        if ($appointment->invoice) {
            return $appointment->invoice;
        }

        $invoice = Invoice::create([
            'number' => Invoice::nextNumber(),
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'issued_on' => today(),
            'due_on' => today()->addDays(7),
            'created_by' => auth()->id(),
        ]);

        $invoice->items()->create([
            'service_id' => $appointment->service_id,
            'description' => $appointment->service?->name ?? 'Dental consultation',
            'quantity' => 1,
            'unit_price' => $appointment->fee,
        ]);

        $invoice->recalculate();

        return $invoice;
    }
}
