@props(['title' => null, 'subtitle' => null, 'actions' => null, 'padded' => true, 'accent' => false, 'icon' => null])

<section {{ $attributes->merge(['class' => 'card-flat overflow-hidden ' . ($accent ? 'panel-accent' : '')]) }}>
    @if($title || $actions)
        <header class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-pine/8 bg-gradient-to-b from-porcelain/60 to-white">
            <div class="flex items-center gap-3 min-w-0">
                @if($icon)
                    <span class="chip-icon chip-icon-sm">
                        <svg class="w-[15px] h-[15px]" viewBox="0 0 20 20" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
                    </span>
                @endif
                <div class="min-w-0">
                    @if($title)<h2 class="font-display text-[1.0625rem] font-semibold truncate">{{ $title }}</h2>@endif
                    @if($subtitle)<p class="text-[13px] text-pine/60 mt-0.5">{{ $subtitle }}</p>@endif
                </div>
            </div>
            @if($actions)<div class="flex items-center gap-2">{{ $actions }}</div>@endif
        </header>
    @endif

    <div class="{{ $padded ? 'p-5' : '' }}">
        {{ $slot }}
    </div>
</section>
