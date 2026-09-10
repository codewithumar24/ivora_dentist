@extends('layouts.console')
@section('heading', 'Patients')
@section('subheading', number_format($patients->total()).' on the register')

@section('actions')
    @permission('patients.export')
        <a href="{{ route('admin.patients.export', $filters) }}" class="btn btn-ghost btn-sm">Export</a>
    @endpermission
    @permission('patients.create')
        <a href="{{ route('admin.patients.create') }}" class="btn btn-primary btn-sm">Register patient</a>
    @endpermission
@endsection

@section('content')
<div class="space-y-5">
    <form method="GET" class="card-flat filter-bar p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="field-label">Search</label>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email or phone" class="field !py-2 !text-sm">
        </div>
        <div>
            <label class="field-label">Status</label>
            <select name="status" class="field !py-2 !text-sm">
                <option value="">Any</option>
                @foreach(['active', 'suspended', 'pending'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary btn-sm">Filter</button>
    </form>

    <x-ui.panel :padded="false">
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>MRN</th><th>Patient</th><th>Contact</th><th>Age</th><th>City</th><th>Visits</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td class="font-mono text-xs">{{ $patient->patientProfile?->mrn ?? '—' }}</td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $patient->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium">{{ $patient->name }}</p>
                                        <p class="text-xs text-pine/50 truncate">{{ $patient->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs">{{ $patient->phone }}</td>
                            <td>{{ $patient->age ?? '—' }}</td>
                            <td>{{ $patient->city ?? '—' }}</td>
                            <td class="font-mono text-xs">{{ $patient->appointments_count }}</td>
                            <td>
                                <span class="badge badge-{{ $patient->status === 'active' ? 'jade' : 'slate' }}">{{ ucfirst($patient->status) }}</span>
                            </td>
                            <td class="text-right"><a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-ghost btn-sm">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-ui.empty title="No patients found" message="Try a different search." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $patients->links() }}
</div>
@endsection
