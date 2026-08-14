@extends('emails.layout')
@section('preheader', 'Confirm your email address')

@section('body')
    <p style="margin:0 0 18px;font-size:22px;font-weight:600;letter-spacing:-.02em;">One quick confirmation</p>

    <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#33514C;">
        Confirming this address means appointment reminders actually reach you rather than sitting
        in a spam folder. The link lasts 48 hours.
    </p>

    <x-mail-button :url="$url" label="Confirm my email address" />

    <p style="margin:20px 0 0;font-size:12px;line-height:1.7;color:#8AA39E;word-break:break-all;">{{ $url }}</p>
@endsection
