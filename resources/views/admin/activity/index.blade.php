@extends('layouts.console')
@section('heading', 'Audit trail')
@section('subheading', 'Who did what, and when')

@section('content')
<div class="space-y-5">
    <form method="GET" class="card-flat filter-bar p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="field-label">Search</label>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="field !py-2 !text-sm">
        </div>
        <div>
            <label class="field-label">Action</label>
            <select name="action" class="field !py-2 !text-sm">
                <option value="">Any</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>{{ Str::headline($action) }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary btn-sm">Filter</button>
    </form>

    <x-ui.panel :padded="false">
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>When</th><th>Who</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-xs">
                                {{ $log->created_at->format('j M Y') }}
                                <span class="block font-mono text-pine/45">{{ $log->created_at->format('H:i') }}</span>
                            </td>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td><span class="badge badge-mint">{{ Str::headline($log->action) }}</span></td>
                            <td class="truncate max-w-[380px]">{{ $log->description }}</td>
                            <td class="font-mono text-xs text-pine/45">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-ui.empty title="Nothing logged yet" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $logs->links() }}
</div>
@endsection
