@extends('layouts.auth')
@section('title', 'Confirm your email')

@section('form')
<span class="w-12 h-12 arch-sm bg-mint text-pine grid place-items-center mb-6">
    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5h14v10H3zM3 6l7 5 7-5"/></svg>
</span>

<h1 class="font-display text-3xl font-bold mb-2">Check your inbox</h1>
<p class="text-sm text-pine/70 mb-8 leading-relaxed">
    We sent a confirmation link to <strong class="text-pine">{{ auth()->user()->email }}</strong>.
    Confirming it means appointment reminders will actually reach you. The link lasts 48 hours.
</p>

<div class="flex flex-wrap gap-3">
    <form action="{{ route('verification.resend') }}" method="POST">
        @csrf
        <button class="btn btn-primary">Send it again</button>
    </form>
    <a href="{{ route('patient.dashboard') }}" class="btn btn-ghost">Skip for now</a>
</div>

<form action="{{ route('logout') }}" method="POST" class="mt-8">
    @csrf
    <button class="text-sm text-pine/55 underline-grow">Sign out</button>
</form>
@endsection
