@extends('layouts.site')
@section('title', 'Gallery — Ivora Dental Studio')

@section('content')
<x-site.page-hero
    eyebrow="Gallery"
    title="The rooms, the team,<br>and some results."
    lede="Every before-and-after here is a real Ivora patient who gave permission for it to be shown."
/>

<section class="py-14">
    <div class="max-w-shell mx-auto px-5">
        <div class="flex flex-wrap gap-2 mb-10">
            <a href="{{ route('gallery') }}" class="btn btn-sm {{ !$active ? 'btn-primary' : 'btn-ghost' }}">Everything</a>
            @foreach($categories as $category)
                <a href="{{ route('gallery', ['category' => $category]) }}"
                   class="btn btn-sm {{ $active === $category ? 'btn-primary' : 'btn-ghost' }}">{{ $category }}</a>
            @endforeach
        </div>

        @if($items->isEmpty())
            <x-ui.empty title="No images yet" message="The gallery is being updated." />
        @else
            <div class="columns-1 sm:columns-2 lg:columns-3 gap-5 [column-fill:_balance]">
                @foreach($items as $item)
                    <figure class="break-inside-avoid mb-5 reveal group">
                        <div class="arch-sm overflow-hidden bg-pine/5">
                            <img src="{{ $item->url() }}" alt="{{ $item->title }}" loading="lazy"
                                 class="w-full transition-transform duration-[900ms] group-hover:scale-105">
                        </div>
                        <figcaption class="mt-3 flex items-start justify-between gap-3">
                            <p class="text-sm font-medium">{{ $item->title }}</p>
                            <span class="badge badge-mint shrink-0">{{ $item->category }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>

            <div class="mt-12">{{ $items->links() }}</div>
        @endif
    </div>
</section>
@endsection
