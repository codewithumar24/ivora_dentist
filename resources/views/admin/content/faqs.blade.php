@extends('layouts.console')
@section('heading', 'FAQs')
@section('subheading', 'Answers shown on the site and under each treatment')

@section('content')
<div class="grid lg:grid-cols-[1fr_330px] gap-6 items-start">
    <div class="space-y-6">
        @foreach($faqs as $category => $items)
            <div>
                <h2 class="font-display text-lg font-semibold mb-3">{{ $category }}</h2>
                <div class="space-y-3">
                    @foreach($items as $faq)
                       <x-ui.panel>
    <form action="{{ route('admin.content.faqs.update', $faq) }}" method="POST" class="space-y-3">
        @csrf @method('PATCH')
        <input name="question" value="{{ $faq->question }}" class="field !py-2 !text-sm font-medium">
        <textarea name="answer" rows="3" class="field !text-sm">{{ $faq->answer }}</textarea>
        <div class="flex flex-wrap items-center gap-3">
            <input name="category" value="{{ $faq->category }}" class="field !py-1.5 !w-36 !text-sm">
            <input type="number" name="sort_order" value="{{ $faq->sort_order }}" class="field !py-1.5 !w-16 !text-sm">
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="is_active" value="1" @checked($faq->is_active) class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
                Visible
            </label>

            <div class="flex items-center gap-2 ml-auto">
                <form action="{{ route('admin.content.faqs.destroy', $faq) }}" method="POST" data-confirm="Delete this FAQ?">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm border border-rose-200 text-rose-700 hover:bg-rose-50">Delete</button>
                </form>
                <button class="btn btn-primary btn-sm">Save</button>
            </div>
        </div>
    </form>
</x-ui.panel>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <x-ui.panel title="Add a question">
        <form action="{{ route('admin.content.faqs.store') }}" method="POST" class="space-y-4">
            @csrf
            <x-ui.field label="Question" name="question" required />
            <x-ui.field label="Answer" name="answer" type="textarea" required />
            <div>
                <label class="field-label" for="category">Category</label>
                <input name="category" id="category" list="faqCats" value="General" class="field" required>
                <datalist id="faqCats">
                    <option value="General"></option><option value="Appointments"></option>
                    <option value="Payments"></option><option value="Treatments"></option>
                </datalist>
            </div>
            <x-ui.field label="Sort order" name="sort_order" type="number" value="0" />
            <button class="btn btn-primary w-full">Add FAQ</button>
        </form>
    </x-ui.panel>
</div>
@endsection
