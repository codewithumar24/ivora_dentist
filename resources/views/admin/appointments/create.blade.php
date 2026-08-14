@extends('layouts.console')
@section('heading', 'New appointment')
@section('subheading', 'Booking on behalf of a patient')

@section('content')
<form action="{{ route('admin.appointments.store') }}" method="POST" data-booking
      data-slots-url="{{ route('book.slots') }}" data-days-url="{{ route('book.days') }}"
      class="grid lg:grid-cols-[1fr_320px] gap-6 items-start max-w-5xl">
    @csrf

    <div class="space-y-5">
        <x-ui.panel title="Patient">
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="field-label" for="patient_id">Choose a patient <span class="text-rose-500">*</span></label>
                    <select name="patient_id" id="patient_id" class="field" required>
                        <option value="">Search the register…</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                                {{ $patient->name }} — {{ $patient->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="field-label" for="source">Booked via</label>
                    <select name="source" id="source" class="field">
                        @foreach(['phone' => 'Phone call', 'walk_in' => 'Walk-in', 'admin' => 'Front desk', 'online' => 'Online'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('source', 'phone') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p class="text-xs text-pine/55 mt-4">
                New to the clinic? <a href="{{ route('admin.patients.create') }}" class="text-jade underline">Register them first</a>.
            </p>
        </x-ui.panel>

        <x-ui.panel title="Practitioner and time">
            <div class="mb-5">
                <label class="field-label" for="doctor_id">Practitioner <span class="text-rose-500">*</span></label>
                <select name="doctor_id" id="doctor_id" class="field" required>
                    <option value="">Choose…</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(old('doctor_id') == $doctor->id)>
                            {{ $doctor->full_name }} — {{ $doctor->speciality?->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div data-days class="day-strip mb-5">
                <p class="text-sm text-pine/50 py-4">Pick a practitioner to load their diary.</p>
            </div>

            <div data-slots class="min-h-[70px]">
                <p class="text-sm text-pine/50">Times appear once you pick a day.</p>
            </div>

            <input type="hidden" name="scheduled_on" required>
            <input type="hidden" name="starts_at" required>
        </x-ui.panel>

        <x-ui.panel title="Reason">
            <div class="space-y-5">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="field-label" for="service_id">Treatment</label>
                        <select name="service_id" id="service_id" class="field">
                            <option value="">To be decided</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="visit_type">Visit type</label>
                        <select name="visit_type" id="visit_type" class="field">
                            @foreach(['first_visit' => 'First visit', 'follow_up' => 'Follow-up', 'emergency' => 'Emergency'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('visit_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <x-ui.field label="Notes from the call" name="reason" type="textarea" />
            </div>
        </x-ui.panel>
    </div>

    <aside class="card-flat p-6 lg:sticky lg:top-24">
        <h2 class="font-display text-lg font-semibold mb-4">Summary</h2>
        <div class="flex justify-between text-sm gap-3">
            <span class="text-pine/55">Slot</span>
            <span data-slot-summary class="font-medium text-right">Not chosen</span>
        </div>
        <button class="btn btn-primary w-full mt-6">Book appointment</button>
        <p class="text-xs text-pine/50 mt-3 text-center">The patient gets an email and SMS immediately.</p>
    </aside>
</form>
@endsection
