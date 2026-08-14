@extends('layouts.console')
@section('heading', 'Edit '.$patient->name)
@section('subheading', $patient->patientProfile?->mrn)

@section('content')
<form action="{{ route('admin.patients.update', $patient) }}" method="POST" class="max-w-3xl space-y-5">
    @csrf @method('PUT')

    <x-ui.panel title="Personal details">
        <div class="grid sm:grid-cols-2 gap-5">
            <x-ui.field label="Full name" name="name" :value="$patient->name" required />
            <x-ui.field label="Email" name="email" type="email" :value="$patient->email" required />
            <x-ui.field label="Mobile" name="phone" :value="$patient->phone" required />
            <x-ui.field label="Date of birth" name="date_of_birth" type="date" :value="$patient->date_of_birth?->toDateString()" />
            <div>
                <label class="field-label" for="gender">Gender</label>
                <select name="gender" id="gender" class="field">
                    <option value="">—</option>
                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected($patient->gender === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label" for="status">Account status</label>
                <select name="status" id="status" class="field">
                    @foreach(['active', 'suspended', 'pending'] as $status)
                        <option value="{{ $status }}" @selected($patient->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <x-ui.field label="City" name="city" :value="$patient->city" />
        </div>
        <x-ui.field label="Address" name="address" type="textarea" :value="$patient->address" class="mt-5" />
    </x-ui.panel>

    <x-ui.panel title="Medical history">
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="field-label">Blood group</label>
                <input name="profile[blood_group]" value="{{ $patient->patientProfile?->blood_group }}" class="field">
            </div>
            <div>
                <label class="field-label">Insurance provider</label>
                <input name="profile[insurance_provider]" value="{{ $patient->patientProfile?->insurance_provider }}" class="field">
            </div>
        </div>

        <div class="mt-5 space-y-5">
            <div>
                <label class="field-label">Allergies</label>
                <textarea name="profile[allergies]" rows="2" class="field">{{ $patient->patientProfile?->allergies }}</textarea>
            </div>
            <div>
                <label class="field-label">Medical conditions</label>
                <textarea name="profile[medical_conditions]" rows="2" class="field">{{ $patient->patientProfile?->medical_conditions }}</textarea>
            </div>
            <div>
                <label class="field-label">Clinic notes</label>
                <textarea name="profile[notes]" rows="2" class="field">{{ $patient->patientProfile?->notes }}</textarea>
            </div>
        </div>
    </x-ui.panel>

    <div class="flex flex-wrap gap-3">
        <button class="btn btn-primary">Save changes</button>
        <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>

@permission('patients.delete')
    <form action="{{ route('admin.patients.destroy', $patient) }}" method="POST" class="max-w-3xl mt-4"
          data-confirm="Archive this patient? Their records are kept but hidden.">
        @csrf @method('DELETE')
        <button class="btn btn-sm border border-rose-200 text-rose-700 hover:bg-rose-50">Archive patient</button>
    </form>
@endpermission
@endsection
