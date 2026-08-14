<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment, public string $context = 'status') {}

    public function envelope(): Envelope
    {
        $subject = $this->context === 'rescheduled'
            ? "New time for {$this->appointment->reference}"
            : "{$this->appointment->status_label} — {$this->appointment->reference}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.appointment-status');
    }
}
