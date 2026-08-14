@extends('layouts.console')
@section('heading', 'Clinical note')
@section('subheading', $record->patient->name.' · '.$record->visited_on->format('j F Y'))

@section('actions')
    @permission('records.update')
        <a href="{{ route('admin.records.edit', $record) }}" class="btn btn-ghost btn-sm">Edit</a>
    @endpermission
    @if($record->prescription)
        <a href="{{ route('admin.prescriptions.pdf', $record->prescription) }}" class="btn btn-primary btn-sm">Print prescription</a>
    @endif
@endsection

@section('content')
<div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start max-w-5xl">
    <x-ui.panel title="Note">
        <dl class="space-y-5 text-sm">
            @foreach([
                'Chief complaint' => $record->chief_complaint,
                'Clinical findings' => $record->clinical_findings,
                'Diagnosis' => $record->diagnosis,
                'Treatment performed' => $record->treatment_performed,
                'Advice' => $record->advice,
            ] as $label => $value)
                @if($value)
                    <div>
                        <dt class="eyebrow text-pine/45 mb-2">{{ $label }}</dt>
                        <dd class="text-pine/85 leading-relaxed">{{ $value }}</dd>
                    </div>
                @endif
            @endforeach
        </dl>

        @if($record->teeth_involved)
            <div class="mt-6 pt-5 border-t border-pine/8">
                <p class="eyebrow text-pine/45 mb-2.5">Teeth involved</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($record->teeth_involved as $tooth)
                        <span class="badge badge-pine font-mono">{{ $tooth }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </x-ui.panel>

    <aside class="space-y-5">
        <x-ui.panel title="Patient">
            <div class="flex items-center gap-3">
                <img src="{{ $record->patient->avatar_url }}" alt="" class="w-11 h-11 rounded-xl object-cover">
                <div class="min-w-0">
                    <p class="font-medium truncate">{{ $record->patient->name }}</p>
                    <p class="font-mono text-xs text-pine/50">{{ $record->patient->patientProfile?->mrn }}</p>
                </div>
            </div>
            @permission('patients.view')
                <a href="{{ route('admin.patients.show', $record->patient) }}" class="btn btn-ghost btn-sm w-full mt-4">Full record</a>
            @endpermission
        </x-ui.panel>

        @if($record->prescription)
            <x-ui.panel title="Prescription">
                <p class="font-mono text-xs text-pine/55 mb-3">{{ $record->prescription->reference }}</p>
                <ul class="space-y-2.5 text-sm">
                    @foreach($record->prescription->items as $item)
                        <li class="pb-2.5 border-b border-pine/6 last:border-0">
                            <p class="font-medium">{{ $item->medicine }} {{ $item->dosage }}</p>
                            <p class="text-xs text-pine/55 font-mono">{{ $item->frequency }} · {{ $item->duration }}</p>
                        </li>
                    @endforeach
                </ul>
            </x-ui.panel>
        @endif

        @if($record->next_visit_on)
            <x-ui.panel title="Next review">
                <p class="font-display text-xl font-semibold">{{ $record->next_visit_on->format('j F Y') }}</p>
            </x-ui.panel>
        @endif
    </aside>
</div>
@endsection
