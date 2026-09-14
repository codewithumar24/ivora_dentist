@extends('layouts.portal')
@section('heading', 'Visit on '.$record->visited_on->format('j F Y'))
@section('subheading', 'Seen by '.$record->doctor->full_name)

@section('content')
<div class="grid lg:grid-cols-[1fr_300px] gap-6 items-start max-w-5xl">
    <x-ui.panel title="Clinical note">
        <dl class="space-y-6 text-sm">
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
                <p class="eyebrow text-pine/45 mb-2.5">Teeth involved (FDI notation)</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($record->teeth_involved as $tooth)
                        <span class="badge badge-pine font-mono">{{ $tooth }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </x-ui.panel>

    <aside class="space-y-5">
        @if($record->prescription)
            <x-ui.panel title="Prescription">
                <p class="font-mono text-xs text-pine/55 mb-4">{{ $record->prescription->reference }}</p>
                <ul class="space-y-3 text-sm">
                    @foreach($record->prescription->items as $item)
                        <li class="pb-3 border-b border-pine/6 last:border-0">
                            <p class="font-medium">{{ $item->medicine }} {{ $item->dosage }}</p>
                            <p class="text-xs text-pine/55 mt-0.5 font-mono">{{ $item->frequency }} · {{ $item->duration }}</p>
                            @if($item->instruction)<p class="text-xs text-pine/70 mt-1">{{ $item->instruction }}</p>@endif
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('patient.prescriptions.pdf', $record->prescription) }}" class="btn btn-primary btn-sm w-full mt-5">Download PDF</a>
            </x-ui.panel>
        @endif

        @if($record->next_visit_on)
            <x-ui.panel title="Next review">
                <p class="font-display text-xl font-semibold">{{ $record->next_visit_on->format('j F Y') }}</p>
                <a href="{{ route('patient.appointments.create') }}" class="btn btn-ghost btn-sm w-full mt-4">Book it now</a>
            </x-ui.panel>
        @endif
    </aside>
</div>
@endsection
