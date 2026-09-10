@extends('layouts.site')
@section('title', 'Questions — Ivora Dental Studio')

@section('content')
<x-site.page-hero
    eyebrow="Questions"
    title="Answers, without<br>the hedging."
    lede="If the question you have is not here, the front desk will answer it on the phone without asking you to book first."
/>

<section class="py-16">
    <div class="max-w-shell mx-auto px-5 grid lg:grid-cols-[220px_1fr] gap-12">
        <nav class="reveal lg:sticky lg:top-24 self-start">
            <p class="eyebrow text-pine/45 mb-4">Topics</p>
            <ul class="space-y-1.5 text-sm">
                @foreach($groups as $category => $items)
                    <li><a href="#{{ Str::slug($category) }}" class="block py-1.5 text-pine/70 hover:text-jade transition">{{ $category }} <span class="font-mono text-xs opacity-50">{{ $items->count() }}</span></a></li>
                @endforeach
            </ul>
        </nav>

        <div class="space-y-14">
            @foreach($groups as $category => $items)
                <div id="{{ Str::slug($category) }}" class="reveal scroll-mt-28">
                    <h2 class="font-display text-2xl font-semibold mb-6">{{ $category }}</h2>
                    <div data-accordion class="card-flat divide-y divide-pine/8">
                        @foreach($items as $faq)
                            <div data-accordion-item>
                                <button data-accordion-trigger class="w-full flex items-center justify-between gap-5 text-left px-5 py-4" aria-expanded="false">
                                    <span class="font-medium text-[15px]">{{ $faq->question }}</span>
                                    <svg class="w-4 h-4 text-jade shrink-0" viewBox="0 0 16 16" fill="none"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                                </button>
                                <div data-accordion-panel class="overflow-hidden transition-all duration-500" style="max-height:0">
                                    <p class="text-sm text-pine/70 leading-relaxed px-5 pb-5 pr-10">{{ $faq->answer }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="reveal card-flat p-8 bg-pine text-porcelain">
                <h3 class="font-display text-xl font-semibold mb-2">Still unsure?</h3>
                <p class="text-porcelain/80 text-sm mb-6">Call reception on {{ $settings['phone'] ?? '' }} or send a message — we answer within one working day.</p>
                <a href="{{ route('contact') }}" class="btn btn-light btn-sm">Contact us</a>
            </div>
        </div>
    </div>
</section>
@endsection
