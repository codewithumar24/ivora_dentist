@extends('layouts.site')
@section('title', 'Journal — Ivora Dental Studio')

@section('content')
<x-site.page-hero
    eyebrow="Journal"
    title="Plain answers,<br>written by clinicians."
    lede="No listicles, no scare tactics. Just the explanations we find ourselves giving in the chair, written down."
/>

@if($featured)
    <section class="py-14 border-b border-pine/8">
        <div class="max-w-shell mx-auto px-5">
            <a href="{{ route('blog.show', $featured->slug) }}" class="grid lg:grid-cols-2 gap-10 items-center group reveal">
                <div class="arch-sm aspect-[16/11] bg-pine/5 overflow-hidden">
                    <img src="{{ $featured->cover_url }}" alt="" class="w-full h-full object-cover transition-transform duration-[900ms] group-hover:scale-105">
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="badge badge-mint">Latest</span>
                        @if($featured->category)<span class="badge badge-iris">{{ $featured->category->name }}</span>@endif
                        <span class="font-mono text-xs text-pine/50">{{ $featured->read_minutes }} min</span>
                    </div>
                    <h2 class="font-display text-3xl lg:text-4xl font-bold leading-tight">{{ $featured->title }}</h2>
                    <p class="lede text-pine/70 mt-5">{{ $featured->excerpt }}</p>
                    <p class="text-sm text-pine/55 mt-6 font-mono">
                        {{ $featured->author?->name }} · {{ $featured->published_at?->format('j M Y') }}
                    </p>
                </div>
            </a>
        </div>
    </section>
@endif

<section class="py-14">
    <div class="max-w-shell mx-auto px-5">
        <form method="GET" class="flex flex-wrap items-center gap-3 mb-10">
            <div class="reveal flex flex-wrap gap-2 flex-1">
                <a href="{{ route('blog.index') }}" class="btn btn-sm {{ !$active ? 'btn-primary' : 'btn-ghost' }}">All</a>
                @foreach($categories as $category)
                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                       class="btn btn-sm {{ $active === $category->slug ? 'btn-primary' : 'btn-ghost' }}">
                        {{ $category->name }} <span class="opacity-50 font-mono text-xs ml-1">{{ $category->posts_count }}</span>
                    </a>
                @endforeach
            </div>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search articles" class="field !py-2 !text-sm w-48">
        </form>

        @if($posts->isEmpty())
            <x-ui.empty title="Nothing published here yet" message="Try another category." />
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-9">
                @foreach($posts as $post)
                    <x-site.post-card :post="$post" />
                @endforeach
            </div>
            <div class="mt-12">{{ $posts->links() }}</div>
        @endif
    </div>
</section>
@endsection
