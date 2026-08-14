@extends('layouts.console')
@section('heading', 'New clinical note')
@section('subheading', $appointment ? 'For '.$appointment->patient->name.' · '.$appointment->reference : 'Standalone record')

@section('content')
<form action="{{ route('admin.records.store') }}" method="POST" class="max-w-4xl space-y-5">
    @csrf
    @if($appointment)
        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
    @endif

    <x-ui.panel title="Visit">
        <div class="grid sm:grid-cols-3 gap-5">
            <div>
                <label class="field-label" for="patient_id">Patient <span class="text-rose-500">*</span></label>
                <select name="patient_id" id="patient_id" class="field" required>
                    <option value="">Choose…</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment?->patient_id) == $patient->id)>{{ $patient->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label" for="doctor_id">Practitioner <span class="text-rose-500">*</span></label>
                <select name="doctor_id" id="doctor_id" class="field" required>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(old('doctor_id', $appointment?->doctor_id ?? auth()->user()->doctor?->id) == $doctor->id)>{{ $doctor->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <x-ui.field label="Date of visit" name="visited_on" type="date"
                        :value="$appointment?->scheduled_on->toDateString() ?? now()->toDateString()" required />
        </div>
    </x-ui.panel>

    <x-ui.panel title="Clinical detail">
        <div class="space-y-5">
            <x-ui.field label="Chief complaint" name="chief_complaint"
                        :value="$appointment?->symptoms" hint="In the patient's own words where possible." />
            <x-ui.field label="Clinical findings" name="clinical_findings" type="textarea" />
            <x-ui.field label="Diagnosis" name="diagnosis" type="textarea" />
            <x-ui.field label="Treatment performed" name="treatment_performed" type="textarea" />

            <div>
                <label class="field-label">Teeth involved (FDI)</label>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(['18','17','16','15','14','13','12','11','21','22','23','24','25','26','27','28','48','47','46','45','44','43','42','41','31','32','33','34','35','36','37','38'] as $tooth)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="teeth_involved[]" value="{{ $tooth }}" class="sr-only peer">
                            <span class="w-9 h-8 grid place-items-center rounded-lg border border-pine/12 font-mono text-[11px] peer-checked:bg-pine peer-checked:text-mint peer-checked:border-pine transition">{{ $tooth }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <x-ui.field label="Advice given" name="advice" type="textarea" />
                <x-ui.field label="Next review" name="next_visit_on" type="date" />
            </div>
        </div>
    </x-ui.panel>

    <x-ui.panel title="Prescription" subtitle="Leave empty if nothing was prescribed." data-repeater>
        <div data-repeater-list class="space-y-3">
            @for($i = 0; $i < 2; $i++)
                <div data-repeater-row class="grid sm:grid-cols-[1.4fr_.8fr_.8fr_.8fr_1.2fr_auto] gap-2.5 items-end">
                    <div><label class="field-label !text-[11px]">Medicine</label><input name="medicines[{{ $i }}][medicine]" class="field !py-2 !text-sm"></div>
                    <div><label class="field-label !text-[11px]">Dose</label><input name="medicines[{{ $i }}][dosage]" placeholder="500 mg" class="field !py-2 !text-sm"></div>
                    <div><label class="field-label !text-[11px]">Frequency</label><input name="medicines[{{ $i }}][frequency]" placeholder="1 + 0 + 1" class="field !py-2 !text-sm"></div>
                    <div><label class="field-label !text-[11px]">Duration</label><input name="medicines[{{ $i }}][duration]" placeholder="5 days" class="field !py-2 !text-sm"></div>
                    <div><label class="field-label !text-[11px]">Instruction</label><input name="medicines[{{ $i }}][instruction]" placeholder="After food" class="field !py-2 !text-sm"></div>
                    <button type="button" data-repeater-remove class="btn btn-ghost btn-sm h-[38px]">×</button>
                </div>
            @endfor
        </div>

        <template>
            <div data-repeater-row class="grid sm:grid-cols-[1.4fr_.8fr_.8fr_.8fr_1.2fr_auto] gap-2.5 items-end">
                <div><label class="field-label !text-[11px]">Medicine</label><input name="medicines[__INDEX__][medicine]" class="field !py-2 !text-sm"></div>
                <div><label class="field-label !text-[11px]">Dose</label><input name="medicines[__INDEX__][dosage]" class="field !py-2 !text-sm"></div>
                <div><label class="field-label !text-[11px]">Frequency</label><input name="medicines[__INDEX__][frequency]" class="field !py-2 !text-sm"></div>
                <div><label class="field-label !text-[11px]">Duration</label><input name="medicines[__INDEX__][duration]" class="field !py-2 !text-sm"></div>
                <div><label class="field-label !text-[11px]">Instruction</label><input name="medicines[__INDEX__][instruction]" class="field !py-2 !text-sm"></div>
                <button type="button" data-repeater-remove class="btn btn-ghost btn-sm h-[38px]">×</button>
            </div>
        </template>

        <div class="flex flex-wrap items-center gap-3 mt-4">
            <button type="button" data-repeater-add class="btn btn-ghost btn-sm">Add another medicine</button>
        </div>

        <x-ui.field label="Prescription notes" name="prescription_notes" class="mt-5" />
    </x-ui.panel>

    <div class="flex gap-3">
        <button class="btn btn-primary">Save clinical note</button>
        <a href="{{ route('admin.records.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
