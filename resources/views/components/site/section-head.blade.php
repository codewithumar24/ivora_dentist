@props(['eyebrow' => null, 'title' => '', 'lede' => null, 'align' => 'left', 'tone' => 'dark'])

<div class="reveal {{ $align === 'center' ? 'text-center max-w-2xl mx-auto' : 'max-w-2xl' }}">
    @if($eyebrow)
        <p class="eyebrow {{ $tone === 'light' ? 'text-mint' : 'text-jade' }} mb-4 flex items-center gap-2 {{ $align === 'center' ? 'justify-center' : '' }}">
            <span class="w-6 h-px bg-current opacity-60"></span>{{ $eyebrow }}
        </p>
    @endif
    <h2 class="h-section font-display {{ $tone === 'light' ? 'text-porcelain' : 'text-ink' }}">{!! $title !!}</h2>
    @if($lede)
        <p class="lede mt-5 {{ $tone === 'light' ? 'text-porcelain/70' : 'text-pine/70' }}">{{ $lede }}</p>
    @endif
</div>
