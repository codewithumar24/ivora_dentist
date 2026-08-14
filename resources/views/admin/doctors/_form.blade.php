@php $isEdit = $doctor->exists ?? false; @endphp

<div class="grid lg:grid-cols-2 gap-5">
    <x-ui.panel title="Person">
        <div class="space-y-5">
            <div class="grid sm:grid-cols-2 gap-5">
                <x-ui.field label="Full name" name="name" :value="$doctor->user?->name" required />
                <x-ui.field label="Title" name="title" :value="$doctor->title ?? 'Dr.'" required />
            </div>
            <div class="grid sm:grid-cols-2 gap-5">
                <x-ui.field label="Email" name="email" type="email" :value="$doctor->user?->email" required />
                <x-ui.field label="Mobile" name="phone" :value="$doctor->user?->phone" required />
            </div>

            @unless($isEdit)
                <div>
                    <label class="field-label" for="role_id">Console role</label>
                    <select name="role_id" id="role_id" class="field" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected($role->slug === 'dentist')>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-pine/50 mt-1.5">A login is created and emailed to them automatically.</p>
                </div>
            @endunless

            <div>
                <label class="field-label" for="avatar">Portrait</label>
                <input type="file" name="avatar" id="avatar" accept="image/*" class="field !py-2 text-sm">
            </div>
        </div>
    </x-ui.panel>

    <x-ui.panel title="Professional">
        <div class="space-y-5">
            <div>
                <label class="field-label" for="speciality_id">Speciality</label>
                <select name="speciality_id" id="speciality_id" class="field">
                    <option value="">General dentistry</option>
                    @foreach($specialities as $speciality)
                        <option value="{{ $speciality->id }}" @selected($doctor->speciality_id == $speciality->id)>{{ $speciality->name }}</option>
                    @endforeach
                </select>
            </div>

            <x-ui.field label="Job title" name="designation" :value="$doctor->designation" />
            <x-ui.field label="Qualifications" name="qualifications" :value="$doctor->qualifications" />

            <div class="grid sm:grid-cols-3 gap-4">
                <x-ui.field label="Licence no." name="license_no" :value="$doctor->license_no" />
                <x-ui.field label="Years" name="experience_years" type="number" :value="$doctor->experience_years ?? 0" />
                <x-ui.field label="Fee" name="consultation_fee" type="number" step="0.01" :value="$doctor->consultation_fee ?? 0" />
            </div>
        </div>
    </x-ui.panel>
</div>

<x-ui.panel title="Profile copy" class="mt-5">
    <div class="space-y-5">
        <x-ui.field label="Short bio" name="short_bio" type="textarea" :value="$doctor->short_bio"
                    hint="Shown on cards and search results — keep it under 140 characters." />
        <x-ui.field label="Full biography" name="biography" type="textarea" :value="$doctor->biography" />
    </div>
</x-ui.panel>

<x-ui.panel title="Treatments offered" class="mt-5">
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
        @foreach($services as $service)
            <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-pine/10 text-sm cursor-pointer hover:border-jade transition has-[:checked]:bg-mint/25 has-[:checked]:border-pine">
                <input type="checkbox" name="services[]" value="{{ $service->id }}"
                       @checked($isEdit && $doctor->services->contains($service->id))
                       class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
                <span class="truncate">{{ $service->name }}</span>
            </label>
        @endforeach
    </div>
</x-ui.panel>

@if($isEdit)
    <x-ui.panel title="Visibility" class="mt-5">
        <div class="flex flex-wrap gap-6">
            @foreach(['is_active' => 'Active', 'accepts_online_booking' => 'Bookable online', 'is_featured' => 'Featured on the homepage'] as $field => $label)
                <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" @checked($doctor->$field)
                           class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </x-ui.panel>
@else
    <label class="flex items-center gap-2.5 text-sm cursor-pointer mt-5">
        <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
        Feature on the homepage
    </label>
@endif
