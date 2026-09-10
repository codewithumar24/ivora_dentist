@props(['doctor'])

{{--
    A card, not a bare arch. The old version was a 3:4 arch with the name loose
    underneath, so a missing photograph left a tall empty shape with text
    floating below it. Everything now sits inside one bordered surface, the
    portrait is a calmer 4:3, and the name block carries the card on its own if
    the image never loads.
--}}
<article {{ $attributes->merge(['class' => 'lift reveal group flex flex-col overflow-hidden rounded-[20px] border border-pine/10 bg-white']) }}>
    <a href="{{ route('doctors.show', $doctor->slug) }}" class="relative block overflow-hidden bg-pine/5">
        <img src="{{ $doctor->user->avatar_url }}" alt="{{ $doctor->full_name }}" loading="lazy"
             class="aspect-[4/3] w-full object-cover transition-transform duration-[900ms] group-hover:scale-[1.06]">

        <span class="absolute left-3 top-3">
            <span class="badge badge-mint shadow-sm">{{ $doctor->speciality?->name ?? 'General dentistry' }}</span>
        </span>
    </a>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="font-display text-lg font-semibold leading-tight">
            <a href="{{ route('doctors.show', $doctor->slug) }}" class="underline-grow">{{ $doctor->full_name }}</a>
        </h3>
        <p class="mt-1 text-[0.95rem] text-pine/70">{{ $doctor->designation }}</p>

        <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1.5 border-t border-pine/8 pt-4 font-mono text-xs text-pine/60">
            <span class="flex items-center gap-1 font-medium text-jade">
                <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 14 14" aria-hidden="true"><path d="m7 1 1.8 3.7 4.1.6-3 2.9.7 4.1L7 10.4 3.4 12.3l.7-4.1-3-2.9 4.1-.6z"/></svg>
                {{ number_format((float) $doctor->rating, 1) }}
            </span>
            <span aria-hidden="true">·</span>
            <span>{{ $doctor->experience_years }} yrs</span>
            <span aria-hidden="true">·</span>
            <span>{{ $doctor->reviews_count }} {{ Str::plural('review', $doctor->reviews_count) }}</span>
        </div>

        <a href="{{ route('doctors.show', $doctor->slug) }}"
           class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-jade transition-all group-hover:gap-2.5">
            View profile
            <svg class="h-3.5 w-3.5" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>
</article>
