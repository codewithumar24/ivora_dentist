@props(['post'])

<article class="reveal group">
    <a href="{{ route('blog.show', $post->slug) }}" class="block arch-sm aspect-[16/10] mb-4 bg-pine/5 overflow-hidden">
        <img src="{{ $post->cover_url }}" alt="" loading="lazy"
             class="w-full h-full object-cover transition-transform duration-[900ms] group-hover:scale-105">
    </a>

    <div class="flex items-center gap-3 text-xs text-pine/50 font-mono mb-2">
        @if($post->category)<span class="badge badge-mint">{{ $post->category->name }}</span>@endif
        <span>{{ $post->read_minutes }} min read</span>
    </div>

    <h3 class="font-display text-lg font-semibold leading-snug">
        <a href="{{ route('blog.show', $post->slug) }}" class="underline-grow">{{ $post->title }}</a>
    </h3>
    <p class="text-[0.95rem] text-pine/75 mt-2 leading-relaxed">{{ Str::limit($post->excerpt, 118) }}</p>
</article>
