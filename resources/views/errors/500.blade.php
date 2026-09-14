@extends('layouts.site')
@section('title', 'Something broke')

@section('content')
<section class="py-32">
    <div class="max-w-lg mx-auto px-5 text-center">
        <p class="font-mono text-6xl font-bold text-mint mb-6">500</p>
        <h1 class="font-display text-3xl font-bold mb-4">Something broke at our end</h1>
        <p class="text-pine/75 leading-relaxed mb-8">
            This is not your fault. If you were in the middle of booking, call reception on
            {{ $settings['phone'] ?? '' }} and we will finish it for you.
        </p>
        <a href="{{ route('home') }}" class="btn btn-primary">Back to the homepage</a>
    </div>
</section>
@endsection
