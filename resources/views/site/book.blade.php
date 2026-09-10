@extends('layouts.site')
@section('title', 'Book an appointment — Ivora Dental Studio')

@section('content')
<section class="py-12 lg:py-16">
    <div class="max-w-shell mx-auto px-5">

        <div class="max-w-2xl mb-10">
            <p class="eyebrow text-jade flex items-center gap-2.5 mb-4"><span class="w-8 h-px bg-jade/50"></span>Booking</p>
            <h1 class="font-display text-4xl lg:text-5xl font-bold leading-[1]">Pick a clinician,<br>then pick a time.</h1>
            <p class="lede text-pine/70 mt-5">
                The diary below is live. Everything you can see is genuinely free —
                nothing gets held back for the phone.
            </p>
        </div>

        <form action="{{ route('book.store') }}" method="POST" data-booking
              data-slots-url="{{ route('book.slots') }}" data-days-url="{{ route('book.days') }}"
              class="grid lg:grid-cols-[1fr_380px] gap-7 items-start">
            @csrf

            <div class="space-y-5">

                {{-- Step 1: clinician --}}
                <div class="reveal card-flat p-6 lg:p-7">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded-full bg-pine text-mint font-mono text-xs grid place-items-center shrink-0">01</span>
                        <h2 class="font-display text-lg font-semibold">Who would you like to see?</h2>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($doctors as $doctor)
                            <label class="relative flex items-center gap-3 p-3 rounded-2xl border border-pine/12 cursor-pointer hover:border-jade transition has-[:checked]:border-pine has-[:checked]:bg-mint/20">
                                <input type="radio" name="doctor_id" value="{{ $doctor->id }}" class="sr-only"
                                       {{ (string) old('doctor_id', $selectedDoctor) === (string) $doctor->id ? 'checked' : '' }} required>
                                <img src="{{ $doctor->user->avatar_url }}" alt="" class="w-11 h-11 rounded-xl object-cover shrink-0">
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium truncate">{{ $doctor->full_name }}</span>
                                    <span class="block text-xs text-pine/55 truncate">{{ $doctor->speciality?->name }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('doctor_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                {{-- Step 2: day + slot along the arch --}}
                <div class="reveal card-flat p-6 lg:p-7">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded-full bg-pine text-mint font-mono text-xs grid place-items-center shrink-0">02</span>
                        <h2 class="font-display text-lg font-semibold">When suits you?</h2>
                    </div>

                    <div data-days class="day-strip mb-7">
                        <p class="text-sm text-pine/50 py-4">Choose a clinician above to see their diary.</p>
                    </div>

                    <div data-slots class="min-h-[80px]">
                        <p class="text-sm text-pine/50">Times appear once you pick a day.</p>
                    </div>

                    <input type="hidden" name="scheduled_on" value="{{ old('scheduled_on') }}" required>
                    <input type="hidden" name="starts_at" value="{{ old('starts_at') }}" required>
                    @error('starts_at')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                {{-- Step 3: details --}}
                <div class="reveal card-flat p-6 lg:p-7">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded-full bg-pine text-mint font-mono text-xs grid place-items-center shrink-0">03</span>
                        <h2 class="font-display text-lg font-semibold">What is it for?</h2>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="field-label" for="service_id">Treatment</label>
                            <select name="service_id" id="service_id" class="field">
                                <option value="">Not sure yet — advise me</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ (string) old('service_id', $selectedService) === (string) $service->id ? 'selected' : '' }}>
                                        {{ $service->name }} · {{ $service->price_label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="field-label">Type of visit</label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['first_visit' => 'First visit', 'follow_up' => 'Follow-up', 'emergency' => 'Emergency'] as $value => $label)
                                    <label class="text-center py-2.5 rounded-xl border border-pine/12 text-sm cursor-pointer hover:border-jade transition has-[:checked]:bg-pine has-[:checked]:text-porcelain has-[:checked]:border-pine">
                                        <input type="radio" name="visit_type" value="{{ $value }}" class="sr-only" {{ old('visit_type', 'first_visit') === $value ? 'checked' : '' }}>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <x-ui.field label="Anything we should know?" name="symptoms" type="textarea"
                                    hint="A sentence is enough — it helps us set aside the right amount of chair time." />
                    </div>
                </div>
            </div>

            {{-- Summary rail --}}
            <aside class="lg:sticky lg:top-24 space-y-4">
                <div class="reveal card-flat p-6">
                    <h2 class="font-display text-lg font-semibold mb-5">Your appointment</h2>

                    <div class="space-y-3.5 text-sm">
                        <div class="flex justify-between gap-3">
                            <span class="text-pine/55">Time</span>
                            <span data-slot-summary class="font-medium text-right">Not chosen yet</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="text-pine/55">Location</span>
                            <span class="text-right text-pine/80">{{ Str::limit($settings['address'] ?? '', 40) }}</span>
                        </div>
                    </div>

                    @if(!empty($settings['booking_note']))
                        <p class="text-xs text-pine/70 leading-relaxed mt-5 pt-5 border-t border-pine/8">
                            {{ $settings['booking_note'] }}
                        </p>
                    @endif

                    <button class="btn btn-primary w-full mt-6">
                        @auth Confirm booking @else Continue @endauth
                    </button>

                    @guest
                        <p class="text-xs text-pine/55 mt-3 text-center leading-relaxed">
                            You'll create an account on the next screen — it holds your slot and
                            gives you a portal to move it later.
                        </p>
                    @endguest
                </div>

                <div class="reveal card-flat p-6 bg-mint/25 border-mint">
                    <p class="text-sm font-medium mb-1.5">In pain right now?</p>
                    <p class="text-xs text-pine/70 leading-relaxed mb-4">
                        Do not wait for an online slot. Call us and we will fit you in today.
                    </p>
                    <a href="tel:{{ $settings['phone'] ?? '' }}" class="btn btn-primary btn-sm w-full">{{ $settings['phone'] ?? 'Call now' }}</a>
                </div>
            </aside>
        </form>
    </div>
</section>
@endsection
