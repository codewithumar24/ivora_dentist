@extends('layouts.console')
@section('heading', 'Clinical notes')
@section('subheading', $records->total().' records on file')

@section('actions')
    @permission('records.create')
        <a href="{{ route('admin.records.create') }}" class="btn btn-primary btn-sm">New note</a>
    @endpermission
@endsection

@section('content')
<div class="space-y-5">
    <form method="GET" class="card-flat p-4 flex gap-3 items-end">
        <div class="flex-1">
            <label class="field-label">Search by patient</label>
            <input type="search" name="q" value="{{ request('q') }}" class="field !py-2 !text-sm">
        </div>
        <button class="btn btn-primary btn-sm">Search</button>
    </form>

    <x-ui.panel :padded="false">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Date</th><th>Patient</th><th>Practitioner</th><th>Diagnosis</th><th>Next review</th><th></th></tr></thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td class="whitespace-nowrap">{{ $record->visited_on->format('j M Y') }}</td>
                            <td>{{ $record->patient->name }}</td>
                            <td>{{ $record->doctor->full_name }}</td>
                            <td class="truncate max-w-[240px]">{{ $record->diagnosis ?: '—' }}</td>
                            <td>{{ $record->next_visit_on?->format('j M Y') ?? '—' }}</td>
                            <td class="text-right"><a href="{{ route('admin.records.show', $record) }}" class="btn btn-ghost btn-sm">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-ui.empty title="No clinical notes" message="Notes written after treatment appear here." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $records->links() }}
</div>
@endsection
