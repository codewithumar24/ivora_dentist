@extends('layouts.site')
@section('title', 'Unsubscribed')

@section('content')
<section class="py-32">
    <div class="max-w-lg mx-auto px-5 text-center">
        <span class="w-14 h-14 arch-sm bg-mint text-pine grid place-items-center mx-auto mb-6">
            <svg class="w-6 h-6" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M5 10l3.5 3.5L15 6"/></svg>
        </span>
        <h1 class="font-display text-3xl font-bold mb-4">You're off the list</h1>
        <p class="text-pine/65 leading-relaxed mb-8">
            You will not get any more newsletters from us. Appointment reminders and clinical
            emails still come through — those are part of your care, not marketing.
        </p>
        <a href="{{ route('home') }}" class="btn btn-primary">Back to the website</a>
    </div>
</section>
@endsection
