@extends('layouts.site')
@section('title', $post->meta_title ?: $post->title)
@section('description', $post->meta_description ?: $post->excerpt)

@section('content')
<article>
    <header class="border-b border-pine/8 bg-white">
        <div class="max-w-3xl mx-auto px-5 py-14">
            <nav class="text-xs font-mono text-pine/50 mb-6">
                <a href="{{ route('blog.index') }}" class="hover:text-jade">Journal</a>
                @if($post->category)<span class="mx-2">/</span><span>{{ $post->category->name }}</span>@endif
            </nav>

            <h1 class="font-display text-4xl lg:text-5xl font-bold leading-[1.03]">{{ $post->title }}</h1>
            <p class="lede text-pine/70 mt-6">{{ $post->excerpt }}</p>

            <div class="flex flex-wrap items-center gap-4 mt-8 text-sm text-pine/55">
                @if($post->author)
                    <span class="flex items-center gap-2.5">
                        <img src="{{ $post->author->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                        {{ $post->author->name }}
                    </span>
                @endif
                <span class="font-mono text-xs">{{ $post->published_at?->format('j F Y') }}</span>
                <span class="font-mono text-xs">{{ $post->read_minutes }} min read</span>
            </div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-5">
        <div class="arch-sm aspect-[16/9] -mt-0 mb-12 bg-pine/5 overflow-hidden mt-12">
            <img src="{{ $post->cover_url }}" alt="" class="w-full h-full object-cover">
        </div>

        <div class="reveal prose-ivora text-[17px] pb-16">
            @foreach(preg_split('/\n\s*\n/', $post->body) as $paragraph)
                <p>{!! nl2br(e(trim($paragraph))) !!}</p>
            @endforeach
        </div>
    </div>

    @if($related->isNotEmpty())
        <section class="py-16 bg-white border-t border-pine/8">
            <div class="max-w-shell mx-auto px-5">
                <h2 class="font-display text-2xl font-semibold mb-9">Keep reading</h2>
                <div class="grid sm:grid-cols-3 gap-8">
                    @foreach($related as $item)
                        <x-site.post-card :post="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</article>
@endsection
