@extends('layouts.console')
@section('heading', 'Journal')
@section('subheading', $posts->total().' articles')

@section('actions')
    <a href="{{ route('admin.content.posts.create') }}" class="btn btn-primary btn-sm">Write an article</a>
@endsection

@section('content')
<div class="grid lg:grid-cols-[1fr_300px] gap-6 items-start">
    <div class="space-y-5">
        <form method="GET" class="card-flat filter-bar p-4 flex gap-3 items-end">
            <div class="flex-1"><label class="field-label">Search</label><input type="search" name="q" value="{{ request('q') }}" class="field !py-2 !text-sm"></div>
            <button class="btn btn-primary btn-sm">Search</button>
        </form>

        <x-ui.panel :padded="false">
            <table class="data-table">
                <thead><tr><th>Article</th><th>Category</th><th>Status</th><th>Views</th><th></th></tr></thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>
                                <p class="font-medium truncate max-w-[300px]">{{ $post->title }}</p>
                                <p class="text-xs text-pine/50">{{ $post->published_at?->format('j M Y') }} · {{ $post->author?->name }}</p>
                            </td>
                            <td>@if($post->category)<span class="badge badge-mint">{{ $post->category->name }}</span>@endif</td>
                            <td><span class="badge badge-{{ $post->status === 'published' ? 'jade' : 'slate' }}">{{ ucfirst($post->status) }}</span></td>
                            <td class="font-mono text-xs">{{ number_format($post->views) }}</td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.content.posts.edit', $post) }}" class="btn btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('admin.content.posts.destroy', $post) }}" method="POST" class="inline" data-confirm="Delete this article?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-ui.empty title="Nothing written yet" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.panel>

        {{ $posts->links() }}
    </div>

    <x-ui.panel title="Categories">
        <ul class="space-y-2 text-sm mb-5">
            @foreach($categories as $category)
                <li class="flex justify-between py-1.5 border-b border-pine/6 last:border-0">
                    <span>{{ $category->name }}</span>
                    <span class="font-mono text-xs text-pine/45">{{ $category->slug }}</span>
                </li>
            @endforeach
        </ul>

        <form action="{{ route('admin.content.categories.store') }}" method="POST" class="space-y-3 pt-4 border-t border-pine/8">
            @csrf
            <x-ui.field label="New category" name="name" required />
            <button class="btn btn-ghost btn-sm w-full">Add category</button>
        </form>
    </x-ui.panel>
</div>
@endsection
