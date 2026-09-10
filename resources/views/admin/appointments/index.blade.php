@extends('layouts.console')
@section('heading', 'Appointments')
@section('subheading', $appointments->total().' in the diary')

@section('actions')
    @permission('appointments.export')
        <div data-dropdown class="relative">
            <button data-dropdown-trigger class="btn btn-ghost btn-sm">Export</button>
            <div data-dropdown-menu class="hidden absolute right-0 mt-2 w-44 card-flat shadow-xl p-1.5 z-50">
                @foreach(['xlsx' => 'Excel (.xlsx)', 'csv' => 'CSV', 'pdf' => 'PDF'] as $format => $label)
                    <a href="{{ route('admin.appointments.export', array_merge($filters, ['format' => $format])) }}"
                       class="block px-3 py-2 text-sm rounded-lg hover:bg-mint/30">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    @endpermission
@endsection

@section('content')
<div class="space-y-5">

    <form method="GET" class="card-flat filter-bar p-4 grid sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
        <div class="lg:col-span-2">
            <label class="field-label">Search</label>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Reference or patient" class="field !py-2 !text-sm">
        </div>
        <div>
            <label class="field-label">Status</label>
            <select name="status" class="field !py-2 !text-sm">
                <option value="">Any</option>
                @foreach(config('clinic.appointment_statuses') as $key => $meta)
                    <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label">Practitioner</label>
            <select name="doctor_id" class="field !py-2 !text-sm">
                <option value="">Anyone</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" @selected(($filters['doctor_id'] ?? '') == $doctor->id)>{{ $doctor->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label">From</label>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="field !py-2 !text-sm">
        </div>
        <div class="flex gap-2">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="field !py-2 !text-sm">
            <button class="btn btn-primary btn-sm shrink-0">Filter</button>
        </div>
    </form>

    <x-ui.panel :padded="false">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr><th>Reference</th><th>When</th><th>Patient</th><th>Practitioner</th><th>Treatment</th><th>Status</th><th>Fee</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td class="font-mono text-xs">{{ $appointment->reference }}</td>
                            <td class="whitespace-nowrap">
                                {{ $appointment->scheduled_on->format('j M Y') }}
                                <span class="block text-xs text-pine/50 font-mono">{{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('g:i A') }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $appointment->patient->avatar_url }}" alt="" class="w-7 h-7 rounded-full object-cover">
                                    <span class="truncate max-w-[140px]">{{ $appointment->patient->name }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap">{{ $appointment->doctor->full_name }}</td>
                            <td class="truncate max-w-[160px]">{{ $appointment->service?->name ?? '—' }}</td>
                            <td><x-ui.status :status="$appointment->status" /></td>
                            <td class="font-mono text-xs">{{ number_format((float) $appointment->fee) }}</td>
                            <td class="text-right"><a href="{{ route('admin.appointments.show', $appointment) }}" class="btn btn-ghost btn-sm">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-ui.empty title="No appointments matched" message="Loosen the filters and try again." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $appointments->links() }}
</div>
@endsection
