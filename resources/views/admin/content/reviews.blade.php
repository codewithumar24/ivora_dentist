@extends('layouts.console')
@section('heading', 'Review moderation')
@section('subheading', 'Patient reviews stay hidden until approved')

@section('actions')
    <a href="{{ route('admin.content.reviews', ['pending' => 1]) }}" class="btn btn-ghost btn-sm">Pending only</a>
    <a href="{{ route('admin.content.reviews') }}" class="btn btn-ghost btn-sm">Show all</a>
@endsection

@section('content')
<div class="space-y-4 max-w-4xl">
    @forelse($reviews as $review)
        <x-ui.panel>
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-start gap-3.5 min-w-0">
                    <img src="{{ $review->patient->avatar_url }}" alt="" class="w-10 h-10 rounded-xl object-cover shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5">
                            <p class="font-medium">{{ $review->patient->name }}</p>
                            <span class="flex gap-0.5 text-jade">
                                @for($i = 0; $i < $review->rating; $i++)
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 14 14"><path d="m7 1 1.8 3.7 4.1.6-3 2.9.7 4.1L7 10.4 3.4 12.3l.7-4.1-3-2.9 4.1-.6z"/></svg>
                                @endfor
                            </span>
                        </div>
                        <p class="text-xs text-pine/50 mt-0.5">
                            {{ $review->doctor?->full_name }} · {{ $review->created_at->format('j M Y') }}
                        </p>
                        <p class="text-sm text-pine/80 mt-3 leading-relaxed">{{ $review->body }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.content.reviews.moderate', $review) }}" method="POST" class="flex items-center gap-4 shrink-0">
                    @csrf @method('PATCH')
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="is_approved" value="1" @checked($review->is_approved) class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
                        Publish
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" @checked($review->is_featured) class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
                        Feature
                    </label>
                    <button class="btn btn-primary btn-sm">Save</button>
                </form>
            </div>
        </x-ui.panel>
    @empty
        <x-ui.panel><x-ui.empty title="No reviews" message="Reviews arrive after completed appointments." /></x-ui.panel>
    @endforelse

    {{ $reviews->links() }}
</div>
@endsection
