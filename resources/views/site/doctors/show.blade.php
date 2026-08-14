@extends('layouts.site')
@section('title', $doctor->full_name.' — Ivora Dental Studio')
@section('description', $doctor->short_bio)

@section('content')
<section class="border-b border-pine/8 bg-white">
    <div class="max-w-shell mx-auto px-5 py-14 grid lg:grid-cols-[.85fr_1.15fr] gap-12 items-center">
        <div class="arch-frame aspect-[3/4] max-w-[360px] mx-auto lg:mx-0 bg-pine reveal">
            <img src="{{ $doctor->user->avatar_url }}" alt="{{ $doctor->full_name }}" class="w-full h-full object-cover">
        </div>

        <div>
            <p class="eyebrow text-jade mb-4">{{ $doctor->speciality?->name ?? 'General dentistry' }}</p>
            <h1 class="font-display text-4xl lg:text-5xl font-bold leading-[1] reveal">{{ $doctor->full_name }}</h1>
            <p class="text-lg text-pine/70 mt-3 reveal" data-delay="60">{{ $doctor->designation }}</p>

            <p class="lede text-pine/70 mt-6 max-w-xl reveal" data-delay="120">{{ $doctor->short_bio }}</p>

            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-5 mt-9 reveal" data-delay="180">
                @foreach([
                    ['Experience', $doctor->experience_years.' yrs'],
                    ['Rating', number_format((float) $doctor->rating, 1).' / 5'],
                    ['Reviews', $doctor->reviews_count],
                    ['Consultation', ($settings['currency_symbol'] ?? 'Rs').' '.number_format((float) $doctor->consultation_fee)],
                ] as [$label, $value])
                    <div>
                        <dt class="eyebrow text-pine/45 mb-1.5">{{ $label }}</dt>
                        <dd class="font-display text-xl font-semibold">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="flex flex-wrap gap-3 mt-9 reveal" data-delay="240">
                <a href="{{ route('book', ['doctor' => $doctor->id]) }}" class="btn btn-primary">Book with {{ Str::before($doctor->user->name, ' ') }}</a>
                @if($nextAvailable)
                    <span class="btn btn-ghost pointer-events-none">
                        Next free: {{ $nextAvailable['date']->isToday() ? 'today' : $nextAvailable['date']->format('D j M') }}, {{ $nextAvailable['slot']['label'] }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="py-16">
    <div class="max-w-shell mx-auto px-5 grid lg:grid-cols-[1fr_340px] gap-12">
        <div>
            <div class="prose-ivora max-w-2xl">
                <h2 class="font-display text-2xl font-semibold text-ink">Background</h2>
                <p>{{ $doctor->biography }}</p>
                @if($doctor->qualifications)
                    <p><strong>Qualifications:</strong> {{ $doctor->qualifications }}</p>
                @endif
                @if($doctor->languages)
                    <p><strong>Languages:</strong> {{ implode(', ', $doctor->languages) }}</p>
                @endif
            </div>

            @if($doctor->services->isNotEmpty())
                <div class="mt-12">
                    <h2 class="font-display text-2xl font-semibold mb-6">Treatments offered</h2>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($doctor->services as $service)
                            <a href="{{ route('services.show', $service->slug) }}" class="card-flat px-4 py-3.5 flex items-center justify-between gap-3 hover:border-jade transition">
                                <span class="text-sm">{{ $service->name }}</span>
                                <span class="font-mono text-xs text-pine/50">{{ $service->duration_minutes }}m</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($reviews->isNotEmpty())
                <div class="mt-12">
                    <h2 class="font-display text-2xl font-semibold mb-6">Patient reviews</h2>
                    <div class="space-y-4">
                        @foreach($reviews as $review)
                            <figure class="card-flat p-6">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="flex gap-0.5 text-jade">
                                        @for($i = 0; $i < $review->rating; $i++)
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 14 14"><path d="m7 1 1.8 3.7 4.1.6-3 2.9.7 4.1L7 10.4 3.4 12.3l.7-4.1-3-2.9 4.1-.6z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-pine/50 font-mono">{{ $review->created_at->format('M Y') }}</span>
                                </div>
                                <blockquote class="text-sm text-ink/85 leading-relaxed">{{ $review->body }}</blockquote>
                                <figcaption class="text-xs text-pine/55 mt-3">— {{ Str::before($review->patient?->name ?? 'A patient', ' ') }}</figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <aside class="lg:sticky lg:top-24 self-start space-y-5">
            <div class="card-flat p-6">
                <h3 class="font-display text-lg font-semibold mb-5">Next ten days</h3>
                <div class="space-y-2">
                    @foreach($days as $day)
                        <div class="flex items-center justify-between gap-3 py-2 border-b border-pine/6 last:border-0">
                            <span class="text-sm {{ $day['open'] ? '' : 'text-pine/40' }}">
                                {{ $day['is_today'] ? 'Today' : $day['weekday'].' '.$day['day'].' '.$day['month'] }}
                            </span>
                            @if($day['open'])
                                <span class="badge badge-jade">{{ $day['open'] }} free</span>
                            @else
                                <span class="badge badge-slate">closed</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('book', ['doctor' => $doctor->id]) }}" class="btn btn-primary w-full mt-5">Choose a time</a>
            </div>
        </aside>
    </div>
</section>
@endsection
