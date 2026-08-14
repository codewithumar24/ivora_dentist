@extends('layouts.auth')
@section('title', 'Choose a new password')

@section('form')
<h1 class="font-display text-3xl font-bold mb-2">Choose a new password</h1>
<p class="text-sm text-pine/60 mb-8">Eight characters minimum, with at least one letter and one number.</p>

<form action="{{ route('password.update') }}" method="POST" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <x-ui.field label="Email address" name="email" type="email" :value="$email" required readonly />

    <div>
        <label class="field-label" for="password">New password</label>
        <input id="password" name="password" type="password" required autofocus class="field" autocomplete="new-password">
        @error('password')<p class="field-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="field-label" for="password_confirmation">Confirm new password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required class="field" autocomplete="new-password">
    </div>

    <button class="btn btn-primary w-full">Save new password</button>
</form>
@endsection
