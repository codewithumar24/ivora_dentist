@extends('emails.layout')
@section('preheader', 'From the practice')
@section('footer', 'You subscribed to occasional updates from Ivora Dental Studio.')

@section('body')
    <p style="margin:0 0 18px;font-size:22px;font-weight:600;letter-spacing:-.02em;">{{ $heading }}</p>

    <div style="font-size:15px;line-height:1.7;color:#33514C;white-space:pre-line;">{{ $body }}</div>

    <x-mail-button :url="route('book')" label="Book an appointment" />

    <p style="margin:24px 0 0;font-size:11px;color:#8AA39E;">
        Not interested any more? <a href="{{ $unsubscribeUrl }}" style="color:#1C7A6E;">Unsubscribe</a> —
        you will still get appointment reminders, which are part of your care.
    </p>
@endsection
