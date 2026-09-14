@extends('layouts.auth')
@section('title', 'Reset your password')

@section('form')
<div class="enter delay-1 text-center">
    <h1 class="auth-title">Forgot <em>Password</em></h1>
    <p class="mt-2.5 text-pine/70">Give us the address on your account and we'll email a reset link. It expires in an hour.</p>
</div>

<form action="{{ route('password.email') }}" method="POST" class="enter delay-2 mt-8 space-y-4">
    @csrf
    <x-ui.auth-field label="Email address" name="email" type="email" placeholder="you@example.com" required autofocus />
    <button class="btn btn-primary w-full">Email me a link</button>
</form>

<p class="enter delay-3 text-sm text-pine/70 mt-8 text-center">
    Remembered it? <a href="{{ route('login') }}" class="link-grow font-semibold text-jade">Back to sign in</a>
</p>
@endsection
