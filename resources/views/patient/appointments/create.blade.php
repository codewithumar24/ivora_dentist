@extends('layouts.portal')
@section('heading', 'Book a visit')
@section('subheading', 'Live availability, straight from the diary')

@section('content')
<form action="{{ route('patient.appointments.store') }}" method="POST" data-booking
      data-slots-url="{{ route('book.slots') }}" data-days-url="{{ route('book.days') }}"
      class="grid lg:grid-cols-[1fr_340px] gap-6 items-start max-w-5xl">
    @csrf

    <div class="space-y-5">
        <x-ui.panel title="Choose a clinician">
            <div class="grid sm:grid-cols-2 gap-3">
                @foreach($doctors as $doctor)
                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-pine/12 cursor-pointer hover:border-jade transition has-[:checked]:border-pine has-[:checked]:bg-mint/20">
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
        </x-ui.panel>

        <x-ui.panel title="Pick a day and time">
            <div data-days class="day-strip mb-6">
                <p class="text-sm text-pine/50 py-4">Choose a clinician first.</p>
            </div>
            <div data-slots class="min-h-[70px]">
                <p class="text-sm text-pine/50">Times appear once you pick a day.</p>
            </div>
            <input type="hidden" name="scheduled_on" required>
            <input type="hidden" name="starts_at" required>
        </x-ui.panel>

        <x-ui.panel title="Details">
            <div class="space-y-5">
                <div>
                    <label class="field-label" for="service_id">Treatment</label>
                    <select name="service_id" id="service_id" class="field">
                        <option value="">Not sure — please advise</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label">Type of visit</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['first_visit' => 'First visit', 'follow_up' => 'Follow-up', 'emergency' => 'Emergency'] as $value => $label)
                            <label class="text-center py-2.5 rounded-xl border border-pine/12 text-sm cursor-pointer hover:border-jade transition has-[:checked]:bg-pine has-[:checked]:text-porcelain">
                                <input type="radio" name="visit_type" value="{{ $value }}" class="sr-only" {{ old('visit_type', 'follow_up') === $value ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <x-ui.field label="What's bothering you?" name="symptoms" type="textarea" />
            </div>
        </x-ui.panel>
    </div>

    <aside class="card-flat p-6 lg:sticky lg:top-24">
        <h2 class="font-display text-lg font-semibold mb-5">Summary</h2>
        <div class="text-sm space-y-3">
            <div class="flex justify-between gap-3">
                <span class="text-pine/55">Time</span>
                <span data-slot-summary class="font-medium text-right">Not chosen</span>
            </div>
        </div>
        <button class="btn btn-primary w-full mt-6">Confirm booking</button>
        <p class="text-xs text-pine/50 mt-3 leading-relaxed text-center">
            You can move or cancel this yourself up to {{ config('clinic.cancel_window_hours') }} hours before.
        </p>
    </aside>
</form>
@endsection
