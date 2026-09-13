@extends('layouts.site')

@section('title', ($settings['meta_title'] ?? 'Zynera Dental Solutions'))

@section('content')

{{-- ═══ Hero ══════════════════════════════════════════════════════════════ --}}
@php
    // Settings › Home. Uploaded through the admin console, so reception can
    // swap the footage without a developer.
    $heroVideo  = \App\Models\Setting::media('hero_video');
    $heroPhoto  = \App\Models\Setting::media('hero_image', 'images/home.jpg');
    $heroPoster = \App\Models\Setting::media('hero_poster') ?: $heroPhoto;
@endphp

<section class="hero-stage relative overflow-hidden {{ $heroVideo ? 'has-video' : '' }}">
    @if ($heroVideo)
        {{-- Full-bleed background loop. Decorative, so it is muted,
             un-focusable and hidden from assistive tech; the wash above it
             keeps the headline legible over any frame. --}}
        <video class="hero-video" autoplay muted loop playsinline preload="metadata"
               poster="{{ $heroPoster }}" aria-hidden="true" tabindex="-1">
            <source src="{{ $heroVideo }}" type="video/mp4">
        </video>
        <div class="hero-wash"></div>
    @endif

    <div class="max-w-shell mx-auto px-5 pt-20 pb-20 lg:pt-14 lg:pb-14 relative z-10">
        <div class="grid lg:grid-cols-[1.05fr_.95fr] gap-12 lg:gap-16 items-center">

            <div>
                <!-- <p class="eyebrow text-jade flex items-center gap-2.5 mb-6 reveal">
                    Karachi · Est. 2020
                </p> -->

                <h1 class="h-display font-display reveal" data-delay="60">
                    Dentistry<br>
                    without the<br>
                    <span class="relative inline-block">
                        <span class="relative z-10">dread.</span>
                        <svg class="absolute -bottom-2 left-0 w-full" height="14" viewBox="0 0 200 14" fill="none" preserveAspectRatio="none">
                            <path d="M2 11C40 4 90 2 198 6" stroke="#A8E6D8" stroke-width="7" stroke-linecap="round"/>
                        </svg>
                    </span>
                </h1>

                <p class="lede text-pine/70 mt-7 max-w-lg reveal" data-delay="140">
                    We show you the x-ray, explain what we are looking at, and price the work
                    before anyone picks up an instrument. Six chairs, ten clinicians, and
                    appointments that start on time.
                </p>

                <div class="flex flex-wrap items-center gap-3 mt-9 reveal" data-delay="200">
                    <a href="{{ route('book') }}" class="btn btn-primary">
                        Book an appointment
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a href="tel:{{ $settings['phone'] ?? '' }}" class="btn btn-ghost">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3h3l1 3-2 1a8 8 0 0 0 4 4l1-2 3 1v3a1 1 0 0 1-1 1A11 11 0 0 1 2 4a1 1 0 0 1 1-1z"/></svg>
                        {{ $settings['phone'] ?? 'Call reception' }}
                    </a>
                </div>

                {{-- Real availability, pulled from the diary --}}
                @if($nextOpen)
                    <div class="mt-9 inline-flex items-center gap-4 glass rounded-2xl px-5 py-3.5 reveal" data-delay="260">
                        <span class="pulse-dot w-2 h-2 rounded-full bg-jade text-jade shrink-0"></span>
                        <div>
                            <p class="text-[11px] font-mono uppercase tracking-widest text-pine/50">Next free slot</p>
                            <p class="text-sm font-medium text-pine">
                                {{ $nextOpen['date']->isToday() ? 'Today' : $nextOpen['date']->format('D j M') }},
                                {{ $nextOpen['slot']['label'] }}
                            </p>
                        </div>
                        <a href="{{ route('book') }}" class="btn btn-accent btn-sm">Take it</a>
                    </div>
                @endif
            </div>

            {{-- The arch: hero image framed by Ivora's shape --}}
            <!-- <div class="relative reveal" data-delay="120">
                <div class="arch-frame aspect-[18/15] max-w-[840px] mx-auto bg-pine">
                    <img src="{{ $heroPhoto }}"
                         alt="A treatment room at {{ $settings['clinic_name'] ?? 'the studio' }}"
                         class="w-full h-full object-cover">
                </div>

                {{-- Floating credential card --}}
                <div class="absolute -left-2 lg:-left-8 bottom-10 glass rounded-2xl p-4 w-[190px] shadow-xl">
                    <div class="flex -space-x-2.5 mb-3">
                        @foreach($doctors->take(4) as $doctor)
                            <img src="{{ $doctor->user->avatar_url }}" alt=""
                                 class="w-8 h-8 rounded-full border-2 border-white object-cover">
                        @endforeach
                    </div>
                    <p class="text-xs text-pine/70 leading-snug">
                        <span class="font-semibold text-pine">Ten clinicians</span>, each with a
                        declared speciality — not a rota of locums.
                    </p>
                </div>

                <div class="absolute -right-2 lg:-right-6 top-12 glass rounded-2xl px-4 py-3 shadow-xl">
                    <p class="font-display text-2xl font-bold text-pine leading-none"><span data-count="98">0</span>%</p>
                    <p class="text-[11px] text-pine/70 mt-1">would come back</p>
                </div>
            </div> -->
        </div>
    </div>

   
</section>

 {{-- Ticker --}}
    <div class="border-y border-pine/10 py-4 overflow-hidden relative z-10">
        <div class="marquee font-mono text-lg font-semibold uppercase tracking-[.2em] text-ink/80">
            @for($i = 0; $i < 2; $i++)
                @foreach(['Digital x-rays', 'Sedation available', 'Wheelchair accessible', 'Same-week emergencies', 'Written treatment plans', 'Instalments on plans over Rs 50,000', 'Children welcome'] as $item)
                   <span class="flex items-center gap-3 shrink-0">{{ $item }} <span class="text-jade">◆</span></span>
                @endforeach
            @endfor
        </div>
    </div>

{{-- ═══ Treatments ════════════════════════════════════════════════════════ --}}
<section class="py-24">
    <div class="max-w-shell mx-auto px-5">
        <div class="flex flex-wrap items-end justify-between gap-6 mb-14">
            <x-site.section-head
                eyebrow="What we do"
                title="Treatments, with the price<br>attached to each one."
                lede="No consultation-only quotes. Every treatment on this site lists what it costs and how long it takes."
            />
            <a href="{{ route('services.index') }}" class="btn btn-ghost reveal">All treatments</a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($services as $service)
                <x-site.service-card :service="$service" />
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ Approach (dark) ═══════════════════════════════════════════════════ --}}
<x-site.arch-divider from="#F4F7F5" to="#10403B" />

<section class="on-dark relative overflow-hidden">
    <div class="grain absolute inset-0"></div>
    <div class="relative max-w-shell mx-auto px-5 py-24">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <x-site.section-head
                    eyebrow="How we work"
                    tone="light"
                    title="Three things we do differently."
                    lede="None of them are complicated. All of them are unusual."
                />

                <div class="mt-12 space-y-8" data-accordion>
                    @foreach([
                        ['We show you the screen', 'Your x-rays and intraoral photos go up on the monitor in front of you. If we say a tooth needs work, you see the same thing we see, and you get to ask why.'],
                        ['Quotes come in writing', 'Before anything is booked you get an itemised plan with costs, alternatives, and what happens if you do nothing. It goes to your portal so you can think about it at home.'],
                        ['Appointments start on time', 'We book realistic slot lengths rather than stacking the diary. If we are running more than ten minutes late, the front desk calls you before you leave the house.'],
                    ] as $i => [$heading, $body])
                        <div data-accordion-item class="{{ $i === 0 ? 'is-open' : '' }} border-b border-white/10 pb-8 reveal">
                            <button data-accordion-trigger class="w-full flex items-start gap-5 text-left" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                                <span class="font-mono text-xs text-mint/60 pt-1.5">0{{ $i + 1 }}</span>
                                <span class="flex-1 font-display text-xl font-semibold text-porcelain">{{ $heading }}</span>
                                <svg class="w-4 h-4 text-mint mt-1.5 shrink-0" viewBox="0 0 16 16" fill="none"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            </button>
                            <div data-accordion-panel class="overflow-hidden transition-all duration-500" style="max-height: {{ $i === 0 ? '200px' : '0' }}">
                                <p class="text-porcelain/80 leading-relaxed pl-11 pt-4">{{ $body }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 reveal">
                @foreach([
                    ['patients', 'patients treated', $stats['patients']],
                    ['years', 'years in practice', $stats['years']],
                    ['chairs', 'treatment chairs', $stats['chairs']],
                    ['satisfaction', '% satisfaction', $stats['satisfaction']],
                ] as $i => [$key, $label, $value])
                    <div class="glass !bg-white/8 !border-white/12 rounded-2xl p-6 {{ $i % 2 === 1 ? 'translate-y-6' : '' }}">
    <p class="font-display text-4xl font-bold text-black leading-none">
        <span data-count="{{ $value }}">0</span>{{ $key === 'patients' ? '+' : '' }}
    </p>

    <p class="text-xs text-black mt-2.5 leading-snug">
        {{ $label }}
    </p>
</div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<x-site.arch-divider from="#10403B" to="#F4F7F5" flip />

{{-- ═══ Practitioners ═════════════════════════════════════════════════════ --}}
<section class="py-24">
    <div class="max-w-shell mx-auto px-5">
        <div class="flex flex-wrap items-end justify-between gap-6 mb-14">
            <x-site.section-head
                eyebrow="Who you'll see"
                title="The people, not<br>the practice."
                lede="You are booked with a named clinician and you see that clinician. If they are away, we tell you before you arrive."
            />
            <a href="{{ route('doctors.index') }}" class="btn btn-ghost reveal">Meet everyone</a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-7">
            @foreach($doctors as $doctor)
                <x-site.doctor-card :doctor="$doctor" />
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ Booking steps along the arch ══════════════════════════════════════ --}}
<section class="py-20 bg-white border-y border-pine/8">
    <div class="max-w-shell mx-auto px-5">
        <x-site.section-head
            eyebrow="Booking"
            align="center"
            title="Four steps, about ninety seconds."
        />

        <div class="relative mt-16">
            {{-- the arch the steps sit on --}}
            <svg class="hidden lg:block absolute inset-x-0 top-8 w-full h-24" viewBox="0 0 1200 100" fill="none" preserveAspectRatio="none" aria-hidden="true">
                <path d="M40 92C220 18 420 6 600 6s380 12 560 86" stroke="#A8E6D8" stroke-width="2" stroke-dasharray="6 8"/>
            </svg>

            <div class="relative grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach([
                    ['Pick a clinician', 'Filter by treatment or speciality. Each profile shows their next free slot.'],
                    ['Choose a time', 'The diary is live — everything you can see is genuinely available.'],
                    ['Tell us why', 'A sentence is enough. It lets us set aside the right amount of chair time.'],
                    ['Get confirmation', 'Email and SMS straight away, then a reminder the day before.'],
                ] as $i => [$title, $body])
                    <div class="text-center reveal" data-delay="{{ $i * 90 }}">
                        <span class="w-12 h-12 rounded-full bg-pine text-mint font-mono text-sm grid place-items-center mx-auto mb-5 relative z-10 ring-8 ring-white">
                            0{{ $i + 1 }}
                        </span>
                        <h3 class="font-display font-semibold text-base mb-2">{{ $title }}</h3>
                        <p class="text-sm text-pine/70 leading-relaxed">{{ $body }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="text-center mt-14">
            <a href="{{ route('book') }}" class="btn btn-primary">Start booking</a>
        </div>
    </div>
</section>

{{-- ═══ Testimonials ══════════════════════════════════════════════════════ --}}
<section class="py-24 overflow-hidden">
    <div class="max-w-shell mx-auto px-5">
        <x-site.section-head
            eyebrow="In their words"
            title="What patients say when<br>we are not in the room."
        />
    </div>

<div class="mt-14 testimonial-mask overflow-hidden">
    <div class="testimonial-track">
        @for($rep = 0; $rep < 2; $rep++)
            @foreach($testimonials as $testimonial)
                <figure class="card-flat p-7 w-[320px] sm:w-[360px] shrink-0 flex flex-col">
                    <div class="flex gap-1 mb-5 text-jade">
                        @for($i = 0; $i < $testimonial->rating; $i++)
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 14 14"><path d="m7 1 1.8 3.7 4.1.6-3 2.9.7 4.1L7 10.4 3.4 12.3l.7-4.1-3-2.9 4.1-.6z"/></svg>
                        @endfor
                    </div>

                    <blockquote class="text-[15px] leading-relaxed text-ink/85 flex-1">
                        “{{ $testimonial->quote }}”
                    </blockquote>

                    <figcaption class="flex items-center gap-3 mt-6 pt-5 border-t border-pine/8">
                        <img src="{{ $testimonial->avatar_url }}" alt="" class="w-9 h-9 rounded-full object-cover">
                        <div>
                            <p class="text-sm font-medium">{{ $testimonial->name }}</p>
                            <p class="text-xs text-pine/55 font-mono">{{ $testimonial->treatment }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        @endfor
    </div>
</div>
</section>

{{-- ═══ Journal ═══════════════════════════════════════════════════════════ --}}
@if($posts->isNotEmpty())
<section class="py-24 bg-white border-t border-pine/8">
    <div class="max-w-shell mx-auto px-5">
        <div class="flex flex-wrap items-end justify-between gap-6 mb-14">
            <x-site.section-head
                eyebrow="Journal"
                title="Plain answers to<br>the usual questions."
            />
            <a href="{{ route('blog.index') }}" class="btn btn-ghost reveal">Read the journal</a>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <x-site.post-card :post="$post" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══ FAQ + contact ═════════════════════════════════════════════════════ --}}
<section class="py-24">
    <div class="max-w-shell mx-auto px-5 grid lg:grid-cols-[1fr_.85fr] gap-16">
        <div>
            <x-site.section-head eyebrow="Common questions" title="Before you book." />

            <div class="mt-10 space-y-0" data-accordion>
                @foreach($faqs as $i => $faq)
                    <div data-accordion-item class="border-b border-pine/10 reveal">
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

            <a href="{{ route('faq') }}" class="inline-flex items-center gap-2 text-sm text-jade mt-8 underline-grow">
                All questions
                <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>

        {{-- Visit card --}}
        <aside class="reveal">
            <div class="reveal card-flat overflow-hidden sticky top-24">
                <div class="aspect-[4/3] bg-pine/5">
                    <iframe src="{{ $settings['map_embed'] ?? '' }}" class="w-full h-full border-0" loading="lazy" title="Map to Ivora Dental Studio"></iframe>
                </div>

                <div class="p-6">
                    <h3 class="font-display text-lg font-semibold mb-4">Find us</h3>

                    <dl class="space-y-3.5 text-sm">
                        <div class="flex gap-3">
                            <dt class="w-20 shrink-0 text-pine/50 font-mono text-xs uppercase tracking-wider pt-0.5">Address</dt>
                            <dd class="text-pine/80">{{ $settings['address'] ?? '' }}</dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-20 shrink-0 text-pine/50 font-mono text-xs uppercase tracking-wider pt-0.5">Phone</dt>
                            <dd><a href="tel:{{ $settings['phone'] ?? '' }}" class="text-jade underline-grow">{{ $settings['phone'] ?? '' }}</a></dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-20 shrink-0 text-pine/50 font-mono text-xs uppercase tracking-wider pt-0.5">Hours</dt>
                            <dd class="text-pine/80">
                                {{ $settings['hours_weekday'] ?? '' }} Mon–Fri<br>
                                {{ $settings['hours_saturday'] ?? '' }} Sat
                            </dd>
                        </div>
                    </dl>

                    <a href="{{ route('contact') }}" class="btn btn-ghost w-full mt-6">Send a message</a>
                </div>
            </div>
        </aside>
    </div>
</section>

{{-- ═══ Closing CTA ═══════════════════════════════════════════════════════ --}}
<section class="max-w-shell mx-auto px-5 pb-8">
    <div class="on-dark rounded-[28px] relative overflow-hidden px-8 py-16 lg:px-16 lg:py-20 text-center">
        <div class="grain absolute inset-0"></div>

        <div class="relative">
            <h2 class="reveal h-section font-display text-porcelain max-w-2xl mx-auto">
                Book the appointment you have been putting off.
            </h2>
            <p class="lede text-porcelain/80 mt-5 max-w-lg mx-auto">
                It takes ninety seconds and you can move it later from your own portal.
            </p>
            <div class="flex flex-wrap justify-center gap-3 mt-9">
                <a href="{{ route('book') }}" class="btn btn-light">Book a visit</a>
                <a href="{{ route('pricing') }}" class="btn btn-light !border-white/25 !text-black-400 hover:!bg-white hover:!text-pine">See prices first</a>
            </div>
        </div>
    </div>
</section>

@endsection
