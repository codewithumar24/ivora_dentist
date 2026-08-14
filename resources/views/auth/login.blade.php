@extends('layouts.auth')
@section('title', 'Sign in — Ivora Dental Studio')

@section('form')
<h1 class="font-display text-3xl font-bold mb-2">Welcome back</h1>
<p class="text-sm text-pine/60 mb-8">Sign in to see your appointments, records and invoices.</p>

<form action="{{ route('login') }}" method="POST" class="space-y-5">
    @csrf

    <x-ui.field label="Email address" name="email" type="email" required autofocus autocomplete="email" />

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="field-label !mb-0">Password</label>
            <a href="{{ route('password.request') }}" class="text-xs text-jade underline-grow">Forgot it?</a>
        </div>
        <input id="password" name="password" type="password" required autocomplete="current-password" class="field">
        @error('password')<p class="field-error">{{ $message }}</p>@enderror
    </div>

    <label class="flex items-center gap-2.5 text-sm text-pine/70 cursor-pointer">
        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
        Keep me signed in on this device
    </label>

    <button class="btn btn-primary w-full">Sign in</button>
</form>

<p class="text-sm text-pine/60 mt-8 text-center">
    New here? <a href="{{ route('register') }}" class="text-jade font-medium underline-grow">Create a patient account</a>
</p>

<div class="mt-10 pt-6 border-t border-pine/10">
    <p class="eyebrow text-pine/40 mb-3">Demo accounts</p>
    <div class="grid grid-cols-2 gap-2 text-xs font-mono text-pine/55">
        <span>owner@ivoradental.com</span><span class="text-right">password</span>
        <span>reception@ivoradental.com</span><span class="text-right">password</span>
        <span>patient@example.com</span><span class="text-right">password</span>
    </div>
</div>
@endsection
