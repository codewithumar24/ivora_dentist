@extends('layouts.console')
@section('heading', 'Gallery')
@section('subheading', 'Clinic photos and before-and-afters')

@section('content')
<div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">
    <div class="space-y-5">
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($items as $item)
                <div class="card-flat overflow-hidden">
                    <img src="{{ $item->url() }}" alt="" class="w-full aspect-[4/3] object-cover">
                    <div class="p-4 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium truncate">{{ $item->title }}</p>
                            <span class="badge badge-mint mt-1.5">{{ $item->category }}</span>
                        </div>
                        <form action="{{ route('admin.content.gallery.destroy', $item) }}" method="POST" data-confirm="Remove this image?">
                            @csrf @method('DELETE')
                            <button class="text-xs text-rose-600 hover:underline shrink-0">Remove</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="sm:col-span-2 xl:col-span-3"><x-ui.panel><x-ui.empty title="Gallery is empty" message="Add the first image on the right." /></x-ui.panel></div>
            @endforelse
        </div>

        {{ $items->links() }}
    </div>

    <x-ui.panel title="Add an image">
        <form action="{{ route('admin.content.gallery.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <x-ui.field label="Title" name="title" required />
            <div>
                <label class="field-label" for="category">Category</label>
                <input name="category" id="category" list="galleryCats" value="Clinic" class="field" required>
                <datalist id="galleryCats">
                    <option value="Clinic"></option><option value="Team"></option><option value="Before & after"></option>
                </datalist>
            </div>
            <div>
                <label class="field-label" for="image">Image file</label>
                <input type="file" name="image" id="image" accept="image/*" class="field !py-2 text-sm">
            </div>
            <x-ui.field label="…or image URL" name="image_url" type="url" />
            <x-ui.field label="Caption" name="caption" type="textarea" />
            <button class="btn btn-primary w-full">Add to gallery</button>
        </form>
    </x-ui.panel>
</div>
@endsection
