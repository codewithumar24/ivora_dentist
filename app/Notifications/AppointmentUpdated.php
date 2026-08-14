<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentUpdated extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment, public string $headline) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'headline' => $this->headline,
            'reference' => $this->appointment->reference,
            'when' => $this->appointment->starts_at_carbon->format('D j M Y, g:i A'),
            'doctor' => $this->appointment->doctor?->full_name,
            'status' => $this->appointment->status,
            'url' => route('patient.appointments.show', $this->appointment->id),
            'icon' => 'calendar',
        ];
    }
}
