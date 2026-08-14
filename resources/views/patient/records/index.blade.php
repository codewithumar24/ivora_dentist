@extends('layouts.portal')
@section('heading', 'Medical records')
@section('subheading', 'Every note written about your care')

@section('content')
<div class="space-y-5 max-w-4xl">
    @forelse($records as $record)
        <x-ui.panel>
            <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
                <div>
                    <p class="font-display text-lg font-semibold">{{ $record->diagnosis ?: $record->chief_complaint ?: 'Clinical note' }}</p>
                    <p class="text-sm text-pine/55 mt-1">
                        {{ $record->visited_on->format('l j F Y') }} · {{ $record->doctor->full_name }}
                    </p>
                </div>
                <a href="{{ route('patient.records.show', $record) }}" class="btn btn-ghost btn-sm">Full note</a>
            </div>

            <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                @foreach(['Complaint' => $record->chief_complaint, 'Treatment' => $record->treatment_performed] as $label => $value)
                    @if($value)
                        <div>
                            <dt class="eyebrow text-pine/45 mb-1.5">{{ $label }}</dt>
                            <dd class="text-pine/80 leading-relaxed">{{ $value }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>

            @if($record->teeth_involved)
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="eyebrow text-pine/45 mr-1 self-center">Teeth</span>
                    @foreach($record->teeth_involved as $tooth)
                        <span class="badge badge-mint">{{ $tooth }}</span>
                    @endforeach
                </div>
            @endif

            @if($record->prescription)
                <div class="mt-5 pt-4 border-t border-pine/8 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-pine/70">
                        Prescription <span class="font-mono text-xs">{{ $record->prescription->reference }}</span>
                        · {{ $record->prescription->items->count() }} item(s)
                    </p>
                    <a href="{{ route('patient.prescriptions.pdf', $record->prescription) }}" class="btn btn-ghost btn-sm">Download PDF</a>
                </div>
            @endif
        </x-ui.panel>
    @empty
        <x-ui.empty title="No records yet" message="Clinical notes appear here after your first completed visit."
                    icon="M5 3h10v14H5zM8 7h4M8 10h4" />
    @endforelse

    {{ $records->links() }}
</div>
@endsection
