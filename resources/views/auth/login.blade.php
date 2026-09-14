@extends('layouts.auth')
@section('title', 'Sign in ' . ($settings['clinic_name'] ?? 'Dental Solution'))

@section('form')
<div class="enter delay-1 text-center">
    <h1 class="auth-title">Welcome <em>Back</em></h1>
    <p class="mt-2.5 text-pine/70">Sign in to see your appointments, records and invoices</p>
</div>

<form action="{{ route('login') }}" method="POST" class="enter delay-2 mt-8 space-y-4">
    @csrf

    <x-ui.auth-field label="Email address" name="email" type="email"
                     placeholder="you@example.com" required autofocus autocomplete="email" />

    <x-ui.auth-field label="Password" name="password" type="password"
                     placeholder="Your password" required autocomplete="current-password" />

    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
        <label class="flex items-center gap-2.5 text-sm text-pine/70 cursor-pointer">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
            Keep me signed in
        </label>

        <a href="{{ route('password.request') }}" class="link-grow text-sm font-semibold text-pine">Forgot password?</a>
    </div>

    <button class="btn btn-primary w-full">Sign in</button>
</form>

<p class="enter delay-3 text-sm text-pine/70 mt-8 text-center">
    New here?
    <a href="{{ route('register') }}" class="link-grow font-semibold text-jade">Create a patient account</a>
</p>
@endsection
