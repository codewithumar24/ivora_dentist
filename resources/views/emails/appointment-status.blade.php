@extends('emails.layout')
@section('preheader', 'Appointment update')

@section('body')
    <p style="margin:0 0 18px;font-size:22px;font-weight:600;letter-spacing:-.02em;">
        @if($context === 'rescheduled')
            Your appointment has moved
        @else
            {{ $appointment->status_label }} — {{ $appointment->reference }}
        @endif
    </p>

    <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#33514C;">
        @switch($appointment->status)
            @case('confirmed') You are booked in. Please arrive ten minutes early if this is your first visit. @break
            @case('completed') Thanks for coming in. Your clinical notes and any prescription are in your portal. @break
            @case('cancelled') This appointment has been cancelled and the slot released. @break
            @case('no_show') We missed you at this appointment. Book again whenever suits. @break
            @default Here is the current state of your booking.
        @endswitch
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F7F5;border-radius:14px;padding:20px;margin-bottom:22px;">
        <tr><td>
            @foreach([
                'When' => $appointment->starts_at_carbon->format('l j F Y').' at '.$appointment->starts_at_carbon->format('g:i A'),
                'With' => $appointment->doctor->full_name,
                'Status' => $appointment->status_label,
                'Reference' => $appointment->reference,
            ] as $label => $value)
                <p style="margin:0 0 12px;font-size:14px;line-height:1.5;">
                    <span style="display:block;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:#8AA39E;margin-bottom:3px;">{{ $label }}</span>
                    <strong style="color:#10403B;">{{ $value }}</strong>
                </p>
            @endforeach
        </td></tr>
    </table>

    <x-mail-button :url="route('patient.appointments.show', $appointment)" label="Open appointment" />
@endsection
