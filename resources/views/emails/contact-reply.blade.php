@extends('emails.layout')
@section('preheader', 'Reply from the front desk')
@section('footer', 'You are receiving this because you contacted Ivora Dental Studio.')

@section('body')
    <p style="margin:0 0 18px;font-size:22px;font-weight:600;letter-spacing:-.02em;">
        Hello {{ \Illuminate\Support\Str::before($message->name, ' ') }},
    </p>

    <div style="font-size:15px;line-height:1.7;color:#33514C;white-space:pre-line;">{{ $message->reply }}</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F7F5;border-radius:14px;padding:18px;margin-top:26px;">
        <tr><td>
            <p style="margin:0 0 6px;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:#8AA39E;">You wrote</p>
            <p style="margin:0;font-size:13px;line-height:1.6;color:#5C7A75;white-space:pre-line;">{{ $message->message }}</p>
        </td></tr>
    </table>

    <x-mail-button :url="route('book')" label="Book an appointment" />
@endsection
