@props(['eyebrow' => null, 'title' => '', 'lede' => null])

<section class="relative overflow-hidden border-b border-pine/8 bg-white">
    <div class="max-w-shell mx-auto px-5 py-16 lg:py-20">
        @if($eyebrow)
            <p class="eyebrow text-jade flex items-center gap-2.5 mb-5 reveal">
                <span class="w-8 h-px bg-jade/50"></span>{{ $eyebrow }}
            </p>
        @endif
        <h1 class="font-display text-4xl lg:text-6xl font-bold leading-[0.98] max-w-3xl reveal" data-delay="60">{!! $title !!}</h1>
        @if($lede)
            <p class="lede text-pine/70 mt-6 max-w-xl reveal" data-delay="120">{{ $lede }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
