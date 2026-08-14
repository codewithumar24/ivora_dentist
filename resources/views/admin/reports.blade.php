@extends('layouts.console')
@section('heading', 'Reports')
@section('subheading', $from->format('j M Y').' – '.$to->format('j M Y'))

@section('actions')
    <a href="{{ route('admin.reports.download', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" class="btn btn-primary btn-sm">Download PDF</a>
@endsection

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="space-y-6">
    <form method="GET" class="card-flat p-4 flex flex-wrap gap-3 items-end">
        <div><label class="field-label">From</label><input type="date" name="from" value="{{ $from->toDateString() }}" class="field !py-2 !text-sm"></div>
        <div><label class="field-label">To</label><input type="date" name="to" value="{{ $to->toDateString() }}" class="field !py-2 !text-sm"></div>
        <button class="btn btn-primary btn-sm">Run report</button>
    </form>

    <div class="grid sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <x-ui.stat label="Appointments" :value="$summary['appointments']" tone="pine" count icon="M4 5h12v12H4zM7 3v4M13 3v4" />
        <x-ui.stat label="Completed" :value="$summary['completed']" tone="jade" count icon="M5 10.5 8.5 14 15 6" />
        <x-ui.stat label="Cancelled" :value="$summary['cancelled']" tone="rose" count icon="M6 6l8 8M14 6l-8 8" />
        <x-ui.stat label="No-shows" :value="$summary['no_shows']" tone="amber" count icon="M10 3v8M10 15h.01" />
        <x-ui.stat label="Collected" :value="$currency.' '.number_format($summary['collected'])" tone="iris" icon="M3 6h14v9H3zM3 9h14" />
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <x-ui.panel title="Revenue, last 12 months">
            <div class="h-[240px]"><canvas id="reportRevenue"></canvas></div>
        </x-ui.panel>
        <x-ui.panel title="Appointments, last 30 days">
            <div class="h-[240px]"><canvas id="reportTrend"></canvas></div>
        </x-ui.panel>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <x-ui.panel title="By treatment" :padded="false">
            <table class="data-table">
                <thead><tr><th>Treatment</th><th class="text-right">Bookings</th><th class="text-right">Value</th></tr></thead>
                <tbody>
                    @forelse($byService as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td class="text-right font-mono">{{ $row->bookings }}</td>
                            <td class="text-right font-mono">{{ $currency }} {{ number_format((float) $row->value) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><x-ui.empty title="Nothing in this range" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.panel>

        <x-ui.panel title="By practitioner" :padded="false">
            <table class="data-table">
                <thead><tr><th>Practitioner</th><th class="text-right">Bookings</th><th class="text-right">Value</th></tr></thead>
                <tbody>
                    @forelse($byDoctor as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td class="text-right font-mono">{{ $row->bookings }}</td>
                            <td class="text-right font-mono">{{ $currency }} {{ number_format((float) $row->value) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><x-ui.empty title="Nothing in this range" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.panel>
    </div>

    <x-ui.panel title="Payments by method" :padded="false">
        <table class="data-table">
            <thead><tr><th>Method</th><th class="text-right">Count</th><th class="text-right">Total</th></tr></thead>
            <tbody>
                @forelse($byMethod as $row)
                    <tr>
                        <td>{{ Str::headline($row->method) }}</td>
                        <td class="text-right font-mono">{{ $row->count }}</td>
                        <td class="text-right font-mono">{{ $currency }} {{ number_format((float) $row->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3"><x-ui.empty title="No payments in this range" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.panel>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart === 'undefined') return;
    Chart.defaults.font.family = '"Inter Tight", sans-serif';
    Chart.defaults.color = '#5C7A75';
    Chart.defaults.plugins.legend.display = false;

    new Chart(document.getElementById('reportRevenue'), {
        type: 'bar',
        data: { labels: @json($revenue['labels']), datasets: [{ data: @json($revenue['values']), backgroundColor: '#A8E6D8', hoverBackgroundColor: '#1C7A6E', borderRadius: 6 }] },
        options: { maintainAspectRatio: false, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: 'rgba(16,64,59,.06)' }, ticks: { callback: v => v >= 1000 ? (v / 1000) + 'k' : v } } } },
    });

    new Chart(document.getElementById('reportTrend'), {
        type: 'line',
        data: { labels: @json($trend['labels']), datasets: [{ data: @json($trend['values']), borderColor: '#1C7A6E', tension: .38, borderWidth: 2.5, pointRadius: 0 }] },
        options: { maintainAspectRatio: false, scales: { x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } }, y: { beginAtZero: true, grid: { color: 'rgba(16,64,59,.06)' }, ticks: { precision: 0 } } } },
    });
});
</script>
@endpush
