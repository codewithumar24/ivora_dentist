@props(['doctor'])

<article class="reveal group">
    <a href="{{ route('doctors.show', $doctor->slug) }}" class="block">
        <div class="arch-frame bg-pine/5 aspect-[3/4] mb-4 relative">
            <img src="{{ $doctor->user->avatar_url }}" alt="{{ $doctor->full_name }}"
                 class="w-full h-full object-cover transition-transform duration-[900ms] group-hover:scale-[1.06]" loading="lazy">
            <div class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-ink/80 to-transparent">
                <span class="badge badge-mint">{{ $doctor->speciality?->name ?? 'General dentistry' }}</span>
            </div>
        </div>
    </a>

    <h3 class="font-display text-lg font-semibold leading-tight">
        <a href="{{ route('doctors.show', $doctor->slug) }}" class="underline-grow">{{ $doctor->full_name }}</a>
    </h3>
    <p class="text-sm text-pine/60 mt-1">{{ $doctor->designation }}</p>

    <div class="flex items-center gap-3 mt-3 text-xs text-pine/55 font-mono">
        <span class="flex items-center gap-1 text-jade">
            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 14 14"><path d="m7 1 1.8 3.7 4.1.6-3 2.9.7 4.1L7 10.4 3.4 12.3l.7-4.1-3-2.9 4.1-.6z"/></svg>
            {{ number_format((float) $doctor->rating, 1) }}
        </span>
        <span>·</span>
        <span>{{ $doctor->experience_years }} yrs</span>
        <span>·</span>
        <span>{{ $doctor->reviews_count }} reviews</span>
    </div>
</article>
