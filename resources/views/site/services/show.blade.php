@extends('layouts.site')
@section('title', $service->name.' — Ivora Dental Studio')
@section('description', $service->summary)

@section('content')
<section class="border-b border-pine/8 bg-white">
    <div class="max-w-shell mx-auto px-5 py-14 grid lg:grid-cols-[1.1fr_.9fr] gap-12 items-center">
        <div>
            <nav class="text-xs text-pine/50 font-mono mb-6 flex items-center gap-2">
                <a href="{{ route('services.index') }}" class="hover:text-jade">Treatments</a>
                <span>/</span>
                <span>{{ $service->speciality?->name }}</span>
            </nav>

            <h1 class="font-display text-4xl lg:text-5xl font-bold leading-[1] reveal">{{ $service->name }}</h1>
            <p class="lede text-pine/70 mt-5 reveal" data-delay="80">{{ $service->summary }}</p>

            <div class="flex flex-wrap gap-3 mt-8 reveal" data-delay="140">
                <span class="badge badge-pine">{{ $service->price_label }}</span>
                <span class="badge badge-mint">{{ $service->duration_minutes }} minutes</span>
                @if($service->speciality)<span class="badge badge-iris">{{ $service->speciality->name }}</span>@endif
            </div>

            <div class="flex flex-wrap gap-3 mt-8 reveal" data-delay="200">
                <a href="{{ route('book', ['service' => $service->id]) }}" class="btn btn-primary">Book this treatment</a>
                <a href="{{ route('contact') }}" class="btn btn-ghost">Ask a question</a>
            </div>
        </div>

        <div class="arch-frame aspect-[4/5] max-w-[400px] mx-auto bg-pine reveal">
            <img src="{{ $service->image_url }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
        </div>
    </div>
</section>

<section class="py-16">
    <div class="max-w-shell mx-auto px-5 grid lg:grid-cols-[1fr_340px] gap-12">
        <div>
            <div class="prose-ivora max-w-2xl">
                <h2 class="font-display text-2xl font-semibold text-ink">What the appointment involves</h2>
                <p>{{ $service->description }}</p>
            </div>

            @if($service->includes)
                <div class="reveal card-flat p-7 mt-10 max-w-2xl">
                    <h3 class="font-display text-lg font-semibold mb-5">Included in the price</h3>
                    <ul class="grid sm:grid-cols-2 gap-3">
                        @foreach($service->includes as $item)
                            <li class="flex items-start gap-2.5 text-sm text-pine/75">
                                <svg class="w-4 h-4 text-jade shrink-0 mt-0.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 8.5 6 11.5 13 4.5"/></svg>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($service->doctors->isNotEmpty())
                <div class="mt-14">
                    <h3 class="font-display text-2xl font-semibold mb-7">Who performs it</h3>
                    <div class="grid sm:grid-cols-3 gap-7">
                        @foreach($service->doctors->take(3) as $doctor)
                            <x-site.doctor-card :doctor="$doctor" />
                        @endforeach
                    </div>
                </div>
            @endif

            @if($faqs->isNotEmpty())
                <div class="mt-14 max-w-2xl" data-accordion>
                    <h3 class="font-display text-2xl font-semibold mb-6">Questions people ask</h3>
                    @foreach($faqs as $faq)
                        <div data-accordion-item class="border-b border-pine/10">
                            <button data-accordion-trigger class="w-full flex items-center justify-between gap-5 text-left py-5" aria-expanded="false">
                                <span class="font-medium text-[15px]">{{ $faq->question }}</span>
                                <svg class="w-4 h-4 text-jade shrink-0" viewBox="0 0 16 16" fill="none"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            </button>
                            <div data-accordion-panel class="overflow-hidden transition-all duration-500" style="max-height:0">
                                <p class="text-sm text-pine/70 leading-relaxed pb-5 pr-8">{{ $faq->answer }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <aside class="space-y-5 lg:sticky lg:top-24 self-start">
            <div class="reveal card-flat p-6">
                <p class="eyebrow text-pine/45 mb-3">Price</p>
                <p class="font-display text-2xl font-bold">{{ $service->price_label }}</p>
                <p class="text-xs text-pine/55 mt-2 leading-relaxed">
                    Where a range is shown, you get an exact figure in writing at the consultation.
                </p>
                <a href="{{ route('book', ['service' => $service->id]) }}" class="btn btn-primary w-full mt-5">Book now</a>
            </div>

            @if($related->isNotEmpty())
                <div class="reveal card-flat p-6">
                    <h3 class="font-display font-semibold mb-4">Related treatments</h3>
                    <ul class="space-y-3">
                        @foreach($related as $item)
                            <li>
                                <a href="{{ route('services.show', $item->slug) }}" class="flex items-center justify-between gap-3 text-sm group">
                                    <span class="underline-grow">{{ $item->name }}</span>
                                    <span class="font-mono text-xs text-pine/50">{{ $item->duration_minutes }}m</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </div>
</section>
@endsection
