@props(['service'])

<a href="{{ route('services.show', $service->slug) }}" class="card p-6 flex flex-col group reveal">
    <span class="w-11 h-11 arch-sm bg-mint/50 text-pine grid place-items-center mb-5 group-hover:bg-pine group-hover:text-mint transition-colors duration-500">
        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 3c2.2 0 3.4 1.2 3.4 3.4 0 1.6-.5 3-.8 4.6-.3 1.7-.4 3.6-1.4 4.7-.6.7-1.8.5-2.1-.4-.4-1.1-.6-2.3-1.1-2.3s-.7 1.2-1.1 2.3c-.3.9-1.5 1.1-2.1.4-1-1.1-1.1-3-1.4-4.7C3.1 9.4 2.6 8 2.6 6.4 2.6 4.2 3.8 3 6 3c1.3 0 1.6.5 2 .5s.7-.5 2-.5z" transform="translate(1)"/>
        </svg>
    </span>

    <h3 class="h-card font-display mb-2">{{ $service->name }}</h3>
    <p class="text-sm text-pine/65 leading-relaxed flex-1">{{ Str::limit($service->summary, 108) }}</p>

    <div class="mt-5 pt-4 border-t border-pine/8 flex items-center justify-between">
        <span class="font-mono text-xs text-pine">{{ $service->price_label }}</span>
        <span class="text-xs text-jade flex items-center gap-1.5 group-hover:gap-2.5 transition-all">
            {{ $service->duration_minutes }} min
            <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
    </div>
</a>
