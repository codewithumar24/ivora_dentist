@props([
    'label' => '',
    'value' => '0',
    'hint' => null,
    'tone' => 'jade',
    'icon' => 'M4 16V8M8 16V4M12 16v-6M16 16v-9',
    'count' => false,
])

@php
    // The gradient cap is set by tone; the text tone keeps the old API working
    // for callers that only passed a colour name.
    $capClass = match ($tone) {
        'iris'  => 'tone-iris',
        'amber' => 'tone-amber',
        'rose'  => 'tone-rose',
        default => '',
    };
@endphp

<div {{ $attributes->merge(['class' => 'card-flat stat-card p-5 ' . $capClass]) }}>
    <div class="relative flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="eyebrow text-pine/55">{{ $label }}</p>

            <p class="stat-figure mt-2.5">
                @if($count)
                    <span data-count="{{ (float) str_replace(',', '', $value) }}">0</span>
                @else
                    {{ $value }}
                @endif
            </p>

            @if($hint)
                <p class="mt-2 text-sm text-pine/70">{{ $hint }}</p>
            @endif
        </div>

        <span class="chip-icon">
            <svg class="w-[18px] h-[18px]" viewBox="0 0 20 20" fill="none" stroke="currentColor"
                 stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
        </span>
    </div>
</div>
