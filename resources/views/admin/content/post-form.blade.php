@extends('layouts.console')
@section('heading', $post->exists ? 'Edit article' : 'Write an article')
@section('subheading', $post->exists ? $post->title : 'It publishes to the journal immediately unless saved as a draft')

@section('content')
<form action="{{ $post->exists ? route('admin.content.posts.update', $post) : route('admin.content.posts.store') }}"
      method="POST" enctype="multipart/form-data" class="grid lg:grid-cols-[1fr_320px] gap-6 items-start max-w-6xl">
    @csrf
    @if($post->exists) @method('PATCH') @endif

    <div class="space-y-5">
        <x-ui.panel title="Content">
            <div class="space-y-5">
                <x-ui.field label="Title" name="title" :value="$post->title" required />
                <x-ui.field label="Excerpt" name="excerpt" type="textarea" :value="$post->excerpt"
                            hint="Shown on cards and in search results. Under 280 characters." />
                <div>
                    <label class="field-label" for="body">Body <span class="text-rose-500">*</span></label>
                    <textarea name="body" id="body" rows="20" class="field font-mono !text-sm leading-relaxed" required>{{ old('body', $post->body) }}</textarea>
                    <p class="text-xs text-pine/50 mt-1.5">Separate paragraphs with a blank line.</p>
                    @error('body')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </x-ui.panel>

        <x-ui.panel title="Search engine">
            <div class="space-y-5">
                <x-ui.field label="Meta title" name="meta_title" :value="$post->meta_title" />
                <x-ui.field label="Meta description" name="meta_description" type="textarea" :value="$post->meta_description" />
            </div>
        </x-ui.panel>
    </div>

    <aside class="space-y-5 lg:sticky lg:top-24">
        <x-ui.panel title="Publishing">
            <div class="space-y-4">
                <div>
                    <label class="field-label" for="status">Status</label>
                    <select name="status" id="status" class="field">
                        @foreach(['published' => 'Published', 'draft' => 'Draft'] as $value => $label)
                            <option value="{{ $value }}" @selected($post->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label" for="post_category_id">Category</label>
                    <select name="post_category_id" id="post_category_id" class="field">
                        <option value="">Uncategorised</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected($post->post_category_id == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <x-ui.field label="Publish date" name="published_at" type="date"
                            :value="$post->published_at?->toDateString() ?? now()->toDateString()" />
                <x-ui.field label="Read time (minutes)" name="read_minutes" type="number" :value="$post->read_minutes ?? 4" />
            </div>
        </x-ui.panel>

        <x-ui.panel title="Cover image">
            @if($post->exists && $post->cover_image)
                <img src="{{ $post->cover_url }}" alt="" class="w-full aspect-video object-cover rounded-xl mb-4">
            @endif
            <input type="file" name="cover_image" accept="image/*" class="field !py-2 text-sm">
            @unless($post->exists)
                <input type="url" name="cover_url" placeholder="…or paste an image URL" class="field !py-2 !text-sm mt-3">
            @endunless
        </x-ui.panel>

        <div class="flex gap-3">
            <button class="btn btn-primary flex-1">{{ $post->exists ? 'Save' : 'Publish' }}</button>
            <a href="{{ route('admin.content.posts') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </aside>
</form>
@endsection
