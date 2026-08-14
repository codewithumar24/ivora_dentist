@extends('layouts.console')
@section('heading', 'Register a patient')
@section('subheading', 'They get portal login details by email')

@section('content')
<form action="{{ route('admin.patients.store') }}" method="POST" class="max-w-3xl space-y-5">
    @csrf

    <x-ui.panel title="Personal details">
        <div class="grid sm:grid-cols-2 gap-5">
            <x-ui.field label="Full name" name="name" required />
            <x-ui.field label="Email" name="email" type="email" required />
            <x-ui.field label="Mobile" name="phone" type="tel" required />
            <x-ui.field label="Date of birth" name="date_of_birth" type="date" />
            <div>
                <label class="field-label" for="gender">Gender</label>
                <select name="gender" id="gender" class="field">
                    <option value="">—</option>
                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('gender') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <x-ui.field label="City" name="city" value="Karachi" />
        </div>
        <x-ui.field label="Address" name="address" type="textarea" class="mt-5" />
    </x-ui.panel>

    <x-ui.panel title="Medical basics" subtitle="Can be completed later by the patient in their portal.">
        <div class="grid sm:grid-cols-2 gap-5">
            <x-ui.field label="Blood group" name="blood_group" />
            <x-ui.field label="Allergies" name="allergies" hint="Anaesthetics, antibiotics, latex." />
        </div>
    </x-ui.panel>

    <div class="flex gap-3">
        <button class="btn btn-primary">Register patient</button>
        <a href="{{ route('admin.patients.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
