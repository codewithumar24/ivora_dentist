@extends('layouts.site')
@section('title', 'Not allowed')

@section('content')
<section class="py-32">
    <div class="max-w-lg mx-auto px-5 text-center">
        <p class="font-mono text-6xl font-bold text-mint mb-6">403</p>
        <h1 class="font-display text-3xl font-bold mb-4">Your account cannot open that</h1>
        <p class="text-pine/75 leading-relaxed mb-8">
            {{ $exception?->getMessage() ?: 'This area needs a permission your role does not currently hold.' }}
            If you think that is wrong, ask the clinic owner to adjust your access.
        </p>
        <a href="{{ url()->previous() }}" class="btn btn-primary">Go back</a>
    </div>
</section>
@endsection
