@extends('layouts.console')
@section('heading', 'Dashboard')
@section('subheading', now()->format('l j F Y'))

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="space-y-6">

    {{-- Headline figures --}}
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-ui.stat label="In the chair today" :value="$headline['appointments_today']" tone="jade" count
                   hint="{{ $headline['appointments_pending'] }} awaiting confirmation"
                   icon="M4 5h12v12H4zM7 3v4M13 3v4M4 9h12" />

        <x-ui.stat label="Patients on file" :value="number_format($headline['patients_total'])" tone="pine"
                   hint="+{{ $headline['patients_new_month'] }} this month"
                   icon="M10 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM4 17a6 6 0 0 1 12 0" />

        <x-ui.stat label="Collected this month" :value="$currency.' '.number_format($headline['revenue_month'])" tone="iris"
                   hint="{{ $headline['revenue_change'] >= 0 ? '▲' : '▼' }} {{ abs($headline['revenue_change']) }}% vs last month"
                   icon="M3 6h14v9H3zM3 9h14M6 12h3" />

        <x-ui.stat label="Outstanding" :value="$currency.' '.number_format($headline['outstanding'])"
                   tone="{{ $headline['outstanding'] > 0 ? 'amber' : 'jade' }}"
                   hint="{{ $headline['unread_messages'] }} unread enquiries"
                   icon="M5 3h10v14l-2.5-1.5L10 17l-2.5-1.5L5 17z" />
    </div>

    @if($personal)
        <div class="card-flat p-5 flex flex-wrap items-center gap-8">
            <p class="eyebrow text-pine/45">Your chair</p>
            @foreach([
                'Today' => $personal['today'],
                'This week' => $personal['week'],
                'Patients seen' => $personal['patients'],
                'Awaiting confirmation' => $personal['pending'],
            ] as $label => $value)
                <div>
                    <p class="font-display text-2xl font-bold leading-none">{{ $value }}</p>
                    <p class="text-xs text-pine/55 mt-1">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Charts --}}
    <div class="grid lg:grid-cols-[1.5fr_1fr] gap-6">
        <x-ui.panel accent title="Appointments, last 30 days" subtitle="Booked visits per day">
            <div class="h-[260px]"><canvas id="trendChart"></canvas></div>
        </x-ui.panel>

        <x-ui.panel accent title="Status split" subtitle="Across the whole diary">
            <div class="h-[260px]"><canvas id="statusChart"></canvas></div>
        </x-ui.panel>
    </div>

    <div class="grid lg:grid-cols-[1.5fr_1fr] gap-6">
        <x-ui.panel accent title="Revenue, last 12 months" subtitle="Payments received">
            <div class="h-[240px]"><canvas id="revenueChart"></canvas></div>
        </x-ui.panel>

        <x-ui.panel accent title="Most-booked treatments">
            <div class="h-[240px]"><canvas id="servicesChart"></canvas></div>
        </x-ui.panel>
    </div>

    {{-- Today's list + pending --}}
    <div class="grid lg:grid-cols-[1.4fr_1fr] gap-6">
        <x-ui.panel accent title="Today's chair list" :subtitle="$today->count().' appointment(s)'" :padded="false">
            <x-slot:actions>
                @permission('appointments.export')
                    <a href="{{ route('admin.appointments.daySheet') }}" class="btn btn-ghost btn-sm">Print day sheet</a>
                @endpermission
                <a href="{{ route('admin.appointments.calendar') }}" class="btn btn-ghost btn-sm">Diary</a>
            </x-slot:actions>

            @forelse($today as $appointment)
                <a href="{{ route('admin.appointments.show', $appointment) }}"
                   class="flex items-center gap-4 px-5 py-3.5 border-b border-pine/6 last:border-0 hover:bg-mint/12 transition">
                    <span class="font-mono text-sm text-pine w-16 shrink-0">{{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('g:i A') }}</span>
                    <img src="{{ $appointment->patient->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $appointment->patient->name }}</p>
                        <p class="text-xs text-pine/55 truncate">{{ $appointment->service?->name ?? 'Consultation' }} · {{ $appointment->doctor->full_name }}</p>
                    </div>
                    <x-ui.status :status="$appointment->status" />
                </a>
            @empty
                <x-ui.empty title="Nothing booked today" message="An unusually quiet day." />
            @endforelse
        </x-ui.panel>

        <div class="space-y-6">
            <x-ui.panel accent title="Awaiting confirmation" :padded="false">
                @forelse($pending as $appointment)
                    <div class="flex items-center gap-3 px-5 py-3 border-b border-pine/6 last:border-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ $appointment->patient->name }}</p>
                            <p class="text-xs text-pine/55">{{ $appointment->scheduled_on->format('j M') }} · {{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('g:i A') }}</p>
                        </div>
                        @permission('appointments.update')
                            <form action="{{ route('admin.appointments.status', $appointment) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button class="btn btn-primary btn-sm">Confirm</button>
                            </form>
                        @endpermission
                    </div>
                @empty
                    <x-ui.empty title="All confirmed" message="Nothing waiting on the front desk." />
                @endforelse
            </x-ui.panel>

            @permission('messages.view')
                <x-ui.panel accent title="New enquiries" :padded="false">
                    <x-slot:actions>
                        <a href="{{ route('admin.inbox.index') }}" class="btn btn-ghost btn-sm">Inbox</a>
                    </x-slot:actions>
                    @forelse($messages as $message)
                        <a href="{{ route('admin.inbox.show', $message) }}" class="block px-5 py-3 border-b border-pine/6 last:border-0 hover:bg-mint/12 transition">
                            <p class="text-sm font-medium truncate">{{ $message->subject ?: 'No subject' }}</p>
                            <p class="text-xs text-pine/55 mt-0.5">{{ $message->name }} · {{ $message->created_at->diffForHumans() }}</p>
                        </a>
                    @empty
                        <x-ui.empty title="Inbox clear" message="Nothing unread." />
                    @endforelse
                </x-ui.panel>
            @endpermission
        </div>
    </div>

    {{-- Lower row --}}
    <div class="grid lg:grid-cols-3 gap-6">
        @permission('patients.view')
            <x-ui.panel accent title="Newest patients" :padded="false">
                @foreach($newPatients as $patient)
                    <a href="{{ route('admin.patients.show', $patient) }}" class="flex items-center gap-3 px-5 py-3 border-b border-pine/6 last:border-0 hover:bg-mint/12 transition">
                        <img src="{{ $patient->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm truncate">{{ $patient->name }}</p>
                            <p class="text-xs text-pine/50">{{ $patient->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @endforeach
            </x-ui.panel>
        @endpermission

        @permission('billing.view')
            <x-ui.panel accent title="Unpaid invoices" :padded="false">
                @foreach($unpaid as $invoice)
                    <a href="{{ route('admin.billing.show', $invoice) }}" class="flex items-center justify-between gap-3 px-5 py-3 border-b border-pine/6 last:border-0 hover:bg-mint/12 transition">
                        <div class="min-w-0">
                            <p class="font-mono text-xs">{{ $invoice->number }}</p>
                            <p class="text-xs text-pine/55 truncate">{{ $invoice->patient->name }}</p>
                        </div>
                        <span class="text-sm font-medium whitespace-nowrap">{{ $currency }} {{ number_format($invoice->balance) }}</span>
                    </a>
                @endforeach
            </x-ui.panel>
        @endpermission

        @permission('activity.view')
            <x-ui.panel accent title="Recent activity" :padded="false">
                @foreach($activity as $log)
                    <div class="px-5 py-2.5 border-b border-pine/6 last:border-0">
                        <p class="text-sm truncate">{{ $log->description }}</p>
                        <p class="text-xs text-pine/45 font-mono mt-0.5">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </x-ui.panel>
        @endpermission
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = '"Inter Tight", sans-serif';
    Chart.defaults.color = '#5C7A75';
    Chart.defaults.plugins.legend.display = false;

    const pine = '#10403B', jade = '#1C7A6E', mint = '#A8E6D8', iris = '#4F5BD5';

    // Appointments trend
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        const gradient = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(28,122,110,.28)');
        gradient.addColorStop(1, 'rgba(28,122,110,0)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($trend['labels']),
                datasets: [{
                    data: @json($trend['values']),
                    borderColor: jade, backgroundColor: gradient, fill: true,
                    tension: .38, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5,
                    pointHoverBackgroundColor: pine,
                }],
            },
            options: {
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: 'rgba(16,64,59,.06)' }, ticks: { precision: 0, font: { size: 10 } } },
                },
            },
        });
    }

    // Status doughnut
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const split = @json($statusSplit);
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: split.map(s => s.label),
                datasets: [{
                    data: split.map(s => s.value),
                    backgroundColor: ['#E8B33C', jade, pine, '#D9736A', '#9BB0AC'],
                    borderWidth: 0, hoverOffset: 8,
                }],
            },
            options: {
                maintainAspectRatio: false, cutout: '64%',
                plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, padding: 14, font: { size: 11 } } } },
            },
        });
    }

    // Revenue bars
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: @json($revenue['labels']),
                datasets: [{ data: @json($revenue['values']), backgroundColor: mint, hoverBackgroundColor: jade, borderRadius: 6, borderSkipped: false }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: 'rgba(16,64,59,.06)' }, ticks: { font: { size: 10 }, callback: v => v >= 1000 ? (v / 1000) + 'k' : v } },
                },
            },
        });
    }

    // Top services
    const servicesCtx = document.getElementById('servicesChart');
    if (servicesCtx) {
        const services = @json($topServices);
        new Chart(servicesCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(services),
                datasets: [{ data: Object.values(services), backgroundColor: iris, borderRadius: 6, borderSkipped: false, barThickness: 14 }],
            },
            options: {
                indexAxis: 'y', maintainAspectRatio: false,
                scales: {
                    x: { beginAtZero: true, grid: { color: 'rgba(16,64,59,.06)' }, ticks: { precision: 0, font: { size: 10 } } },
                    y: { grid: { display: false }, ticks: { font: { size: 10 }, crossAlign: 'far' } },
                },
            },
        });
    }
});
</script>
@endpush
