@props([
    'label' => '',
    'value' => '0',
    'hint' => null,
    'tone' => 'jade',
    'icon' => 'M4 16V8M8 16V4M12 16v-6M16 16v-9',
    'count' => false,
])

@php
    $tones = [
        'jade' => 'text-jade',
        'pine' => 'text-pine',
        'iris' => 'text-iris',
        'amber' => 'text-amber-600',
        'rose' => 'text-rose-600',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'card-flat stat-card p-5 '.($tones[$tone] ?? 'text-jade')]) }}>
    <div class="relative flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="eyebrow text-pine/45">{{ $label }}</p>
            <p class="font-display text-3xl font-bold text-ink mt-2 leading-none">
                @if($count)
                    <span data-count="{{ (float) str_replace(',', '', $value) }}">0</span>
                @else
                    {{ $value }}
                @endif
            </p>
            @if($hint)
                <p class="text-xs text-pine/55 mt-2">{{ $hint }}</p>
            @endif
        </div>
        <span class="w-9 h-9 rounded-xl grid place-items-center bg-current/10 shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
        </span>
    </div>
</div>
