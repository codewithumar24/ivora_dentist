@extends('layouts.console')
@section('heading', 'Edit clinical note')
@section('subheading', $record->patient->name.' · '.$record->visited_on->format('j F Y'))

@section('content')
<form action="{{ route('admin.records.update', $record) }}" method="POST" class="max-w-3xl space-y-5">
    @csrf @method('PATCH')

    <x-ui.panel title="Note">
        <div class="space-y-5">
            <x-ui.field label="Date of visit" name="visited_on" type="date" :value="$record->visited_on->toDateString()" required />
            <x-ui.field label="Chief complaint" name="chief_complaint" :value="$record->chief_complaint" />
            <x-ui.field label="Clinical findings" name="clinical_findings" type="textarea" :value="$record->clinical_findings" />
            <x-ui.field label="Diagnosis" name="diagnosis" type="textarea" :value="$record->diagnosis" />
            <x-ui.field label="Treatment performed" name="treatment_performed" type="textarea" :value="$record->treatment_performed" />
            <x-ui.field label="Advice" name="advice" type="textarea" :value="$record->advice" />
            <x-ui.field label="Next review" name="next_visit_on" type="date" :value="$record->next_visit_on?->toDateString()" />
        </div>
    </x-ui.panel>

    <div class="flex gap-3">
        <button class="btn btn-primary">Save note</button>
        <a href="{{ route('admin.records.show', $record) }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
