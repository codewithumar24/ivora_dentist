@extends('layouts.portal')
@section('heading', 'My details')
@section('subheading', 'Kept private, visible only to your clinicians')

@section('content')
<div class="max-w-3xl space-y-5" data-tabs>

    <div class="flex flex-wrap gap-2">
        @foreach(['personal' => 'Personal', 'medical' => 'Medical history', 'security' => 'Password'] as $key => $label)
            <button data-tab="{{ $key }}" class="btn btn-sm {{ $loop->first ? 'btn-primary is-active' : 'btn-ghost' }}">{{ $label }}</button>
        @endforeach
    </div>

    {{-- Personal --}}
    <div data-tab-panel="personal">
        <x-ui.panel title="Personal details">
            <form action="{{ route('patient.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PATCH')

                <div class="flex items-center gap-5">
                    <img src="{{ $user->avatar_url }}" alt="" class="w-16 h-16 rounded-2xl object-cover">
                    <div class="flex-1">
                        <label class="field-label" for="avatar">Profile photo</label>
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="field !py-2 text-sm">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <x-ui.field label="Full name" name="name" :value="$user->name" required />
                    <x-ui.field label="Mobile" name="phone" :value="$user->phone" required />
                </div>

                <div class="grid sm:grid-cols-3 gap-5">
                    <div>
                        <label class="field-label" for="gender">Gender</label>
                        <select name="gender" id="gender" class="field">
                            <option value="">—</option>
                            @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" @selected($user->gender === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-ui.field label="Date of birth" name="date_of_birth" type="date" :value="$user->date_of_birth?->toDateString()" />
                    <x-ui.field label="City" name="city" :value="$user->city" />
                </div>

                <x-ui.field label="Address" name="address" type="textarea" :value="$user->address" />

                <div class="flex items-center gap-3">
                    <button class="btn btn-primary">Save details</button>
                    <span class="text-xs text-pine/50 font-mono">{{ $profile?->mrn }}</span>
                </div>
            </form>
        </x-ui.panel>
    </div>

    {{-- Medical --}}
    <div data-tab-panel="medical" class="hidden">
        <x-ui.panel title="Medical history" subtitle="Your dentist reads this before every appointment.">
            <form action="{{ route('patient.profile.medical') }}" method="POST" class="space-y-5">
                @csrf @method('PATCH')

                <div class="grid sm:grid-cols-2 gap-5">
                    <x-ui.field label="Blood group" name="blood_group" :value="$profile?->blood_group" />
                    <div class="flex items-end pb-3">
                        <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                            <input type="checkbox" name="is_smoker" value="1" @checked($profile?->is_smoker)
                                   class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
                            I smoke or vape
                        </label>
                    </div>
                </div>

                <x-ui.field label="Allergies" name="allergies" type="textarea" :value="$profile?->allergies"
                            hint="Especially anaesthetics, antibiotics and latex." />
                <x-ui.field label="Medical conditions" name="medical_conditions" type="textarea" :value="$profile?->medical_conditions" />
                <x-ui.field label="Current medications" name="current_medications" type="textarea" :value="$profile?->current_medications" />

                <div class="grid sm:grid-cols-2 gap-5">
                    <x-ui.field label="Emergency contact" name="emergency_contact_name" :value="$profile?->emergency_contact_name" />
                    <x-ui.field label="Their phone" name="emergency_contact_phone" :value="$profile?->emergency_contact_phone" />
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <x-ui.field label="Insurance provider" name="insurance_provider" :value="$profile?->insurance_provider" />
                    <x-ui.field label="Policy number" name="insurance_number" :value="$profile?->insurance_number" />
                </div>

                <button class="btn btn-primary">Save medical details</button>
            </form>
        </x-ui.panel>
    </div>

    {{-- Password --}}
    <div data-tab-panel="security" class="hidden">
        <x-ui.panel title="Change password">
            <form action="{{ route('patient.profile.password') }}" method="POST" class="space-y-5 max-w-sm">
                @csrf @method('PATCH')

                <div>
                    <label class="field-label" for="current_password">Current password</label>
                    <input type="password" name="current_password" id="current_password" class="field" required>
                    @error('current_password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label" for="password">New password</label>
                    <input type="password" name="password" id="password" class="field" required>
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label" for="password_confirmation">Confirm new password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="field" required>
                </div>

                <button class="btn btn-primary">Update password</button>
            </form>
        </x-ui.panel>
    </div>
</div>
@endsection
