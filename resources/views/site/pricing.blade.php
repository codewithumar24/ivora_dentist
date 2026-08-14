@extends('layouts.site')
@section('title', 'Pricing — Ivora Dental Studio')

@section('content')
<x-site.page-hero
    eyebrow="Pricing"
    title="Every treatment,<br>every price."
    lede="These are the prices we charge. Where a range is shown, the consultation will tell you which end of it you are at — and why — before anything is booked."
/>

<section class="py-16">
    <div class="max-w-shell mx-auto px-5 space-y-16">
        @foreach($specialities as $speciality)
            @if($speciality->services->isNotEmpty())
                <div class="reveal">
                    <div class="flex flex-wrap items-baseline justify-between gap-3 mb-6">
                        <h2 class="font-display text-2xl font-semibold">{{ $speciality->name }}</h2>
                        <p class="text-sm text-pine/55 max-w-lg">{{ $speciality->description }}</p>
                    </div>

                    <div class="card-flat overflow-hidden">
                        @foreach($speciality->services as $service)
                            <div class="flex flex-wrap items-center gap-4 px-5 py-4 {{ !$loop->last ? 'border-b border-pine/8' : '' }} hover:bg-mint/15 transition">
                                <div class="flex-1 min-w-[240px]">
                                    <a href="{{ route('services.show', $service->slug) }}" class="font-medium underline-grow">{{ $service->name }}</a>
                                    <p class="text-sm text-pine/60 mt-1">{{ Str::limit($service->summary, 96) }}</p>
                                </div>
                                <span class="font-mono text-xs text-pine/50 w-20">{{ $service->duration_minutes }} min</span>
                                <span class="font-mono text-sm font-medium text-pine w-44 text-right">{{ $service->price_label }}</span>
                                <a href="{{ route('book', ['service' => $service->id]) }}" class="btn btn-ghost btn-sm">Book</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        <div class="card-flat p-8 lg:p-10 bg-mint/25 border-mint">
            <h2 class="font-display text-2xl font-semibold mb-3">Paying for treatment</h2>
            <div class="grid md:grid-cols-3 gap-8 mt-6">
                @foreach([
                    ['Instalments', 'Plans over Rs 50,000 can be split across the course of treatment, interest free. Ask the front desk to set it up.'],
                    ['Insurance', 'We are on the panel for most major local insurers. Bring your card and we check cover before treatment, not after.'],
                    ['Itemised invoices', 'Every invoice breaks down line by line and lands in your patient portal as a PDF the moment it is raised.'],
                ] as [$title, $body])
                    <div>
                        <h3 class="font-display font-semibold mb-2">{{ $title }}</h3>
                        <p class="text-sm text-pine/70 leading-relaxed">{{ $body }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if($faqs->isNotEmpty())
            <div data-accordion class="max-w-3xl">
                <h2 class="font-display text-2xl font-semibold mb-6">Payment questions</h2>
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
</section>
@endsection
