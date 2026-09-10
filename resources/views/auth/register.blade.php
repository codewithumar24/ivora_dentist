@extends('layouts.auth')
@section('title', 'Create an account — Ivora Dental Studio')

@section('form')
<div class="enter delay-1 text-center">
    <h1 class="auth-title">Create your <em>Account</em></h1>
    <p class="mt-2.5 text-pine/70">
    @if($pendingBooking)
        We're holding your slot while you do this — it takes about thirty seconds.
    @else
        One account covers appointments, records, prescriptions and invoices.
    @endif
    </p>
</div>

@if($pendingBooking)
    <div class="card-flat p-4 mb-6 bg-mint/25 border-mint text-sm">
        <p class="font-medium mb-1">Slot on hold</p>
        <p class="text-pine/70 font-mono text-xs">
            {{ \Illuminate\Support\Carbon::parse($pendingBooking['scheduled_on'])->format('D j M Y') }}
            at {{ \Illuminate\Support\Carbon::parse($pendingBooking['starts_at'])->format('g:i A') }}
        </p>
    </div>
@endif

<form action="{{ route('register') }}" method="POST" class="space-y-5">
    @csrf

    <x-ui.auth-field label="Full name" name="name" required autofocus autocomplete="name" />

    <div class="grid sm:grid-cols-2 gap-5">
        <x-ui.auth-field label="Email" name="email" type="email" required autocomplete="email" />
        <x-ui.auth-field label="Mobile" name="phone" type="tel" required hint="For appointment reminders." />
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="field-label" for="gender">Gender</label>
            <select name="gender" id="gender" class="field">
                <option value="">Prefer not to say</option>
                @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('gender') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <x-ui.auth-field label="Date of birth" name="date_of_birth" type="date" />
    </div>

    <x-ui.auth-field label="City" name="city" value="Karachi" />

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-ui.auth-field label="Password" name="password" type="password"
                             placeholder="At least eight characters" required autocomplete="new-password" />
        </div>
        <div>
            <x-ui.auth-field label="Confirm password" name="password_confirmation" type="password"
                             placeholder="Type it once more" required autocomplete="new-password" />
        </div>
    </div>

    <label class="flex items-start gap-2.5 text-sm text-pine/70 cursor-pointer">
        <input type="checkbox" name="terms" value="1" class="w-4 h-4 mt-0.5 rounded border-pine/25 text-pine focus:ring-jade" {{ old('terms') ? 'checked' : '' }}>
        <span>I agree that Ivora may store my medical details for the purpose of my care.</span>
    </label>
    @error('terms')<p class="field-error">{{ $message }}</p>@enderror

    <button class="btn btn-primary w-full">Create account</button>
</form>

<p class="text-sm text-pine/70 mt-8 text-center">
    Already registered? <a href="{{ route('login') }}" class="text-jade font-medium underline-grow">Sign in</a>
</p>
@endsection
