@extends('emails.layout')
@section('preheader', 'Your patient portal is ready')

@section('body')
    <p style="margin:0 0 18px;font-size:22px;font-weight:600;letter-spacing:-.02em;">
        Welcome to Ivora, {{ \Illuminate\Support\Str::before($user->name, ' ') }}
    </p>

    <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#33514C;">
        Your portal is where your appointments, clinical notes, prescriptions and invoices live.
        Everything in one place, and nothing you have to ask reception to dig out.
    </p>

    @if($temporaryPassword)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F7F5;border-radius:14px;padding:20px;margin-bottom:22px;">
            <tr><td>
                <p style="margin:0 0 12px;font-size:14px;">
                    <span style="display:block;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:#8AA39E;margin-bottom:3px;">Email</span>
                    <strong style="color:#10403B;">{{ $user->email }}</strong>
                </p>
                <p style="margin:0;font-size:14px;">
                    <span style="display:block;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:#8AA39E;margin-bottom:3px;">Temporary password</span>
                    <strong style="color:#10403B;font-family:monospace;font-size:16px;">{{ $temporaryPassword }}</strong>
                </p>
                <p style="margin:12px 0 0;font-size:12px;color:#5C7A75;">Please change this the first time you sign in.</p>
            </td></tr>
        </table>
    @endif

    <x-mail-button :url="route('login')" label="Sign in to your portal" />

    <p style="margin:20px 0 0;font-size:13px;line-height:1.7;color:#5C7A75;">
        One thing worth doing now: fill in your medical history under <strong>My details</strong>.
        Your dentist reads it before every appointment.
    </p>
@endsection
