@extends('layouts.auth')
@section('title', 'Reset your password')

@section('form')
<h1 class="font-display text-3xl font-bold mb-2">Forgot your password?</h1>
<p class="text-sm text-pine/60 mb-8">Give us the address on your account and we'll email a reset link. It expires in an hour.</p>

<form action="{{ route('password.email') }}" method="POST" class="space-y-5">
    @csrf
    <x-ui.field label="Email address" name="email" type="email" required autofocus />
    <button class="btn btn-primary w-full">Email me a link</button>
</form>

<p class="text-sm text-pine/60 mt-8 text-center">
    Remembered it? <a href="{{ route('login') }}" class="text-jade font-medium underline-grow">Back to sign in</a>
</p>
@endsection
