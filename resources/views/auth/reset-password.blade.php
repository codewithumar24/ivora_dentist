@extends('layouts.auth')
@section('title', 'Choose a new password')

@section('form')
<div class="enter delay-1 text-center">
    <h1 class="auth-title">Choose a <em>New Password</em></h1>
    <p class="mt-2.5 text-pine/70">Eight characters minimum, with at least one letter and one number.</p>
</div>

<form action="{{ route('password.update') }}" method="POST" class="enter delay-2 mt-8 space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <x-ui.auth-field label="Email address" name="email" type="email" :value="$email" required readonly />

    <x-ui.auth-field label="New password" name="password" type="password"
                     placeholder="At least eight characters" required autofocus autocomplete="new-password" />

    <x-ui.auth-field label="Confirm new password" name="password_confirmation" type="password"
                     placeholder="Type it once more" required autocomplete="new-password" />

    <button class="btn btn-primary w-full">Save new password</button>
</form>
@endsection
