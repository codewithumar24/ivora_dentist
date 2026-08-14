@props(['title' => null, 'subtitle' => null, 'actions' => null, 'padded' => true])

<section {{ $attributes->merge(['class' => 'card-flat overflow-hidden']) }}>
    @if($title || $actions)
        <header class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-pine/8">
            <div>
                @if($title)<h2 class="font-display text-base font-semibold">{{ $title }}</h2>@endif
                @if($subtitle)<p class="text-xs text-pine/55 mt-0.5">{{ $subtitle }}</p>@endif
            </div>
            @if($actions)<div class="flex items-center gap-2">{{ $actions }}</div>@endif
        </header>
    @endif

    <div class="{{ $padded ? 'p-5' : '' }}">
        {{ $slot }}
    </div>
</section>
