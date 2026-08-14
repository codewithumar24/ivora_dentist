@extends('layouts.site')
@section('title', 'Page not found')

@section('content')
<section class="py-32">
    <div class="max-w-lg mx-auto px-5 text-center">
        <p class="font-mono text-6xl font-bold text-mint mb-6">404</p>
        <h1 class="font-display text-3xl font-bold mb-4">That page has moved on</h1>
        <p class="text-pine/65 leading-relaxed mb-8">
            The link may be old, or we may have reorganised something. The treatment list
            and the booking form are both one click away.
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-primary">Back to the homepage</a>
            <a href="{{ route('book') }}" class="btn btn-ghost">Book an appointment</a>
        </div>
    </div>
</section>
@endsection
