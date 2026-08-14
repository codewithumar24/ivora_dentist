@extends('layouts.portal')
@section('heading', 'Prescriptions')
@section('subheading', 'Everything prescribed to you at Ivora')

@section('content')
<div class="space-y-5 max-w-4xl">
    @forelse($prescriptions as $prescription)
        <x-ui.panel>
            <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
                <div>
                    <p class="font-mono text-sm">{{ $prescription->reference }}</p>
                    <p class="text-sm text-pine/55 mt-1">
                        {{ $prescription->issued_on->format('j F Y') }} · {{ $prescription->doctor->full_name }}
                    </p>
                </div>
                <a href="{{ route('patient.prescriptions.pdf', $prescription) }}" class="btn btn-ghost btn-sm">PDF</a>
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left">
                        <th class="pb-2 eyebrow text-pine/45 font-normal">Medicine</th>
                        <th class="pb-2 eyebrow text-pine/45 font-normal">Dose</th>
                        <th class="pb-2 eyebrow text-pine/45 font-normal">Frequency</th>
                        <th class="pb-2 eyebrow text-pine/45 font-normal">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescription->items as $item)
                        <tr class="border-t border-pine/6">
                            <td class="py-2.5 font-medium">{{ $item->medicine }}</td>
                            <td class="py-2.5 font-mono text-xs">{{ $item->dosage }}</td>
                            <td class="py-2.5 font-mono text-xs">{{ $item->frequency }}</td>
                            <td class="py-2.5 font-mono text-xs">{{ $item->duration }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($prescription->notes)
                <p class="text-xs text-pine/60 mt-4 pt-4 border-t border-pine/8">{{ $prescription->notes }}</p>
            @endif
        </x-ui.panel>
    @empty
        <x-ui.empty title="Nothing prescribed yet" message="Prescriptions written at Ivora appear here." icon="M6 4h8v12H6zM9 8h2M10 7v4" />
    @endforelse

    {{ $prescriptions->links() }}
</div>
@endsection
