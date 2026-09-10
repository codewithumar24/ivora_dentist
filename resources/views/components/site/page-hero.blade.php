@props(['eyebrow' => null, 'title' => '', 'lede' => null])

{{-- Every inner page opens on the same gradient band, so the site reads as one
     thing rather than a dozen separately-designed pages. --}}
<section class="site-hero relative overflow-hidden">
    <div class="max-w-shell mx-auto px-5 py-20 lg:py-24 relative z-10">
        @if($eyebrow)
            <p class="eyebrow text-mint flex items-center gap-2.5 mb-5 enter">
                <span class="w-8 h-px bg-mint/60"></span>{{ $eyebrow }}
            </p>
        @endif

        <h1 class="font-display text-4xl lg:text-6xl font-bold leading-[0.98] max-w-3xl text-porcelain enter delay-1">
            {!! $title !!}
        </h1>

        @if($lede)
            <p class="lede text-porcelain/75 mt-6 max-w-xl enter delay-2">{{ $lede }}</p>
        @endif

        <div class="enter delay-3">{{ $slot }}</div>
    </div>
</section>
