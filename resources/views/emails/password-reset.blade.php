@extends('emails.layout')
@section('preheader', 'Password reset')
@section('footer', 'If you did not request this, ignore the email — nothing has changed.')

@section('body')
    <p style="margin:0 0 18px;font-size:22px;font-weight:600;letter-spacing:-.02em;">Reset your password</p>

    <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#33514C;">
        Someone asked to reset the password for {{ $user->email }}. If that was you, use the button below.
        The link expires in {{ config('auth.passwords.users.expire', 60) }} minutes.
    </p>

    <x-mail-button :url="$url" label="Choose a new password" />

    <p style="margin:20px 0 0;font-size:12px;line-height:1.7;color:#8AA39E;word-break:break-all;">
        If the button does not work, paste this into your browser:<br>{{ $url }}
    </p>
@endsection
