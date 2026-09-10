@extends('layouts.site')
@section('title', 'Patient reviews — Ivora Dental Studio')

@section('content')
<x-site.page-hero
    eyebrow="Reviews"
    title="Unedited, and<br>not all five stars."
    lede="We publish what patients write. The occasional four-star review about running late is more useful to you than a wall of perfect scores."
/>

<section class="py-16">
    <div class="max-w-shell mx-auto px-5">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($testimonials as $testimonial)
                <figure class="card-flat p-7 flex flex-col reveal">
                    <div class="flex gap-1 mb-5 text-jade">
                        @for($i = 0; $i < $testimonial->rating; $i++)
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 14 14"><path d="m7 1 1.8 3.7 4.1.6-3 2.9.7 4.1L7 10.4 3.4 12.3l.7-4.1-3-2.9 4.1-.6z"/></svg>
                        @endfor
                    </div>
                    <blockquote class="reveal text-[15px] leading-relaxed text-ink/85 flex-1">“{{ $testimonial->quote }}”</blockquote>
                    <figcaption class="flex items-center gap-3 mt-6 pt-5 border-t border-pine/8">
                        <img src="{{ $testimonial->avatar_url }}" alt="" class="w-9 h-9 rounded-full object-cover">
                        <div>
                            <p class="text-sm font-medium">{{ $testimonial->name }}</p>
                            <p class="text-xs text-pine/55 font-mono">{{ $testimonial->treatment }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>

        <div class="mt-12">{{ $testimonials->links() }}</div>
    </div>
</section>
@endsection
