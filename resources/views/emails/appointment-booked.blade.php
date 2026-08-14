@extends('emails.layout')
@section('preheader', 'Appointment request received')

@section('body')
    <p style="margin:0 0 18px;font-size:22px;font-weight:600;letter-spacing:-.02em;">
        Thanks, {{ \Illuminate\Support\Str::before($appointment->patient->name, ' ') }} — we have your request.
    </p>

    <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#33514C;">
        The front desk confirms bookings during opening hours. You will get a second email once it is
        locked in, and a reminder the day before.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F7F5;border-radius:14px;padding:20px;margin-bottom:22px;">
        <tr><td>
            @foreach([
                'When' => $appointment->starts_at_carbon->format('l j F Y').' at '.$appointment->starts_at_carbon->format('g:i A'),
                'With' => $appointment->doctor->full_name,
                'For' => $appointment->service?->name ?? 'Consultation',
                'Reference' => $appointment->reference,
            ] as $label => $value)
                <p style="margin:0 0 12px;font-size:14px;line-height:1.5;">
                    <span style="display:block;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:#8AA39E;margin-bottom:3px;">{{ $label }}</span>
                    <strong style="color:#10403B;">{{ $value }}</strong>
                </p>
            @endforeach
        </td></tr>
    </table>

    <x-mail-button :url="route('patient.appointments.show', $appointment)" label="View in your portal" />

    <p style="margin:20px 0 0;font-size:13px;line-height:1.7;color:#5C7A75;">
        Need to change it? You can move or cancel from the portal up to
        {{ config('clinic.cancel_window_hours') }} hours before, or call us on
        {{ \App\Models\Setting::get('phone') }}.
    </p>
@endsection
