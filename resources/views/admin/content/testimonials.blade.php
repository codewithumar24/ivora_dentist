@extends('layouts.console')
@section('heading', 'Testimonials')
@section('subheading', 'Quotes shown on the homepage and reviews page')

@section('content')
<div class="grid lg:grid-cols-[1fr_330px] gap-6 items-start">
    <div class="space-y-4">
        @foreach($testimonials as $testimonial)
            <x-ui.panel>
                <form action="{{ route('admin.content.testimonials.update', $testimonial) }}" method="POST" class="space-y-4">
                    @csrf @method('PATCH')

                    <div class="flex items-start gap-4">
                        <img src="{{ $testimonial->avatar_url }}" alt="" class="w-11 h-11 rounded-xl object-cover shrink-0">
                        <div class="grid sm:grid-cols-3 gap-3 flex-1">
                            <input name="name" value="{{ $testimonial->name }}" class="field !py-2 !text-sm" placeholder="Name">
                            <input name="role" value="{{ $testimonial->role }}" class="field !py-2 !text-sm" placeholder="Occupation">
                            <input name="treatment" value="{{ $testimonial->treatment }}" class="field !py-2 !text-sm" placeholder="Treatment">
                        </div>
                    </div>

                    <textarea name="quote" rows="3" class="field !text-sm">{{ $testimonial->quote }}</textarea>

                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <label class="field-label !mb-0 !text-xs">Rating</label>
                            <input type="number" name="rating" value="{{ $testimonial->rating }}" min="1" max="5" class="field !py-1.5 !w-16 !text-sm">
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="field-label !mb-0 !text-xs">Order</label>
                            <input type="number" name="sort_order" value="{{ $testimonial->sort_order }}" class="field !py-1.5 !w-16 !text-sm">
                        </div>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" @checked($testimonial->is_active) class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
                            Visible
                        </label>

                        <button class="btn btn-primary btn-sm ml-auto">Save</button>
                    </div>
                </form>

                <form action="{{ route('admin.content.testimonials.destroy', $testimonial) }}" method="POST"
                      class="mt-3 pt-3 border-t border-pine/8" data-confirm="Delete this testimonial?">
                    @csrf @method('DELETE')
                    <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                </form>
            </x-ui.panel>
        @endforeach

        {{ $testimonials->links() }}
    </div>

    <x-ui.panel title="Add a testimonial">
        <form action="{{ route('admin.content.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <x-ui.field label="Name" name="name" required />
            <x-ui.field label="Occupation" name="role" />
            <x-ui.field label="Treatment" name="treatment" />
            <x-ui.field label="Quote" name="quote" type="textarea" required />
            <x-ui.field label="Rating" name="rating" type="number" value="5" min="1" max="5" required />
            <div>
                <label class="field-label" for="avatar">Photo</label>
                <input type="file" name="avatar" id="avatar" accept="image/*" class="field !py-2 text-sm">
            </div>
            <button class="btn btn-primary w-full">Add testimonial</button>
        </form>
    </x-ui.panel>
</div>
@endsection
