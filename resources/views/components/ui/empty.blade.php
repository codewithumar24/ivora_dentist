@props(['title' => 'Nothing here yet', 'message' => null, 'icon' => 'M4 5h12v12H4zM7 3v4M13 3v4M4 9h12'])

<div {{ $attributes->merge(['class' => 'text-center py-14 px-6']) }}>
    <span class="w-12 h-12 rounded-2xl bg-mint/40 text-pine grid place-items-center mx-auto mb-4">
        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
    </span>
    <p class="font-display text-lg font-semibold">{{ $title }}</p>
    @if($message)<p class="text-sm text-pine/60 mt-1.5 max-w-sm mx-auto">{{ $message }}</p>@endif
    @if(trim($slot))<div class="mt-5">{{ $slot }}</div>@endif
</div>
