@extends('layouts.console')
@section('heading', 'New invoice')

@section('content')
<form action="{{ route('admin.billing.store') }}" method="POST" class="max-w-4xl space-y-5">
    @csrf

    <x-ui.panel title="Details">
        <div class="grid sm:grid-cols-3 gap-5">
            <div>
                <label class="field-label" for="patient_id">Patient <span class="text-rose-500">*</span></label>
                <select name="patient_id" id="patient_id" class="field" required>
                    <option value="">Choose…</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>{{ $patient->name }}</option>
                    @endforeach
                </select>
            </div>
            <x-ui.field label="Issue date" name="issued_on" type="date" :value="now()->toDateString()" required />
            <x-ui.field label="Due date" name="due_on" type="date" :value="now()->addDays(7)->toDateString()" />
        </div>
    </x-ui.panel>

    <x-ui.panel title="Line items" data-repeater>
        <div data-repeater-list class="space-y-3">
            @for($i = 0; $i < 2; $i++)
                <div data-repeater-row class="grid sm:grid-cols-[2fr_.6fr_.9fr_auto] gap-2.5 items-end">
                    <div>
                        <label class="field-label !text-[11px]">Description</label>
                        <input name="items[{{ $i }}][description]" list="serviceList" class="field !py-2 !text-sm" {{ $i === 0 ? 'required' : '' }}>
                    </div>
                    <div><label class="field-label !text-[11px]">Qty</label><input type="number" name="items[{{ $i }}][quantity]" value="1" min="1" class="field !py-2 !text-sm"></div>
                    <div><label class="field-label !text-[11px]">Unit price</label><input type="number" step="0.01" name="items[{{ $i }}][unit_price]" value="0" class="field !py-2 !text-sm"></div>
                    <button type="button" data-repeater-remove class="btn btn-ghost btn-sm h-[38px]">×</button>
                </div>
            @endfor
        </div>

        <template>
            <div data-repeater-row class="grid sm:grid-cols-[2fr_.6fr_.9fr_auto] gap-2.5 items-end">
                <div><label class="field-label !text-[11px]">Description</label><input name="items[__INDEX__][description]" list="serviceList" class="field !py-2 !text-sm"></div>
                <div><label class="field-label !text-[11px]">Qty</label><input type="number" name="items[__INDEX__][quantity]" value="1" min="1" class="field !py-2 !text-sm"></div>
                <div><label class="field-label !text-[11px]">Unit price</label><input type="number" step="0.01" name="items[__INDEX__][unit_price]" value="0" class="field !py-2 !text-sm"></div>
                <button type="button" data-repeater-remove class="btn btn-ghost btn-sm h-[38px]">×</button>
            </div>
        </template>

        <datalist id="serviceList">
            @foreach($services as $service)
                <option value="{{ $service->name }}">{{ $service->price_label }}</option>
            @endforeach
        </datalist>

        <button type="button" data-repeater-add class="btn btn-ghost btn-sm mt-4">Add line</button>

        <div class="grid sm:grid-cols-2 gap-5 mt-6 pt-5 border-t border-pine/8">
            <x-ui.field label="Discount" name="discount" type="number" step="0.01" value="0" />
            <x-ui.field label="Tax" name="tax" type="number" step="0.01" value="0" />
        </div>

        <x-ui.field label="Notes" name="notes" type="textarea" class="mt-5" />
    </x-ui.panel>

    <div class="flex gap-3">
        <button class="btn btn-primary">Raise invoice</button>
        <a href="{{ route('admin.billing.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
