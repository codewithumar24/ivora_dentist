@extends('layouts.portal')
@section('heading', 'Hello, '.Str::before(auth()->user()->name, ' '))
@section('subheading', now()->format('l j F Y'))

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="space-y-6">

    {{-- Next appointment --}}
    @if($next)
        <div class="on-dark rounded-3xl relative overflow-hidden p-7 lg:p-9">
            <div class="grain absolute inset-0"></div>
            <div class="relative flex flex-wrap items-center gap-8 justify-between">
                <div>
                    <p class="eyebrow text-mint/70 mb-3">Your next visit</p>
                    <p class="font-display text-3xl lg:text-4xl font-bold text-porcelain leading-tight">
                        {{ $next->starts_at_carbon->isToday() ? 'Today' : $next->starts_at_carbon->format('l j F') }},
                        {{ $next->starts_at_carbon->format('g:i A') }}
                    </p>
                    <p class="text-porcelain/80 mt-3">
                        {{ $next->service?->name ?? 'Consultation' }} with {{ $next->doctor->full_name }}
                        <span class="font-mono text-xs text-mint/60 ml-2">{{ $next->reference }}</span>
                    </p>
                    <p class="text-sm text-porcelain/50 mt-2">
                        {{ $next->starts_at_carbon->diffForHumans() }} · {{ $settings['address'] ?? '' }}
                    </p>
                </div>

                <div class="flex flex-col gap-2.5">
                    <a href="{{ route('patient.appointments.show', $next) }}" class="btn btn-light">View details</a>
                    <x-ui.status :status="$next->status" class="justify-center" />
                </div>
            </div>
        </div>
    @else
        <div class="card-flat p-8 flex flex-wrap items-center justify-between gap-6">
            <div>
                <h2 class="font-display text-xl font-semibold">No appointment booked</h2>
                <p class="text-sm text-pine/70 mt-1.5">Most adults need a check-up every six months.</p>
            </div>
            <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary">Book a visit</a>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.stat label="Upcoming" :value="$stats['upcoming']" tone="jade" count
                   icon="M4 5h12v12H4zM7 3v4M13 3v4M4 9h12" />
        <x-ui.stat label="Visits completed" :value="$stats['completed']" tone="pine" count
                   icon="M5 10.5 8.5 14 15 6" />
        <x-ui.stat label="Records on file" :value="$stats['records']" tone="iris" count
                   icon="M5 3h10v14H5zM8 7h4M8 10h4" />
        <x-ui.stat label="Balance due" :value="$currency.' '.number_format($stats['due'])"
                   tone="{{ $stats['due'] > 0 ? 'amber' : 'jade' }}"
                   hint="{{ $stats['due'] > 0 ? 'Payable at reception or online' : 'Nothing outstanding' }}"
                   icon="M5 3h10v14l-2.5-1.5L10 17l-2.5-1.5L5 17z" />
    </div>

    <div class="grid lg:grid-cols-[1.3fr_1fr] gap-6">

        {{-- Upcoming list --}}
        <x-ui.panel title="Upcoming appointments" :padded="false">
            <x-slot:actions>
                <a href="{{ route('patient.appointments.index') }}" class="btn btn-ghost btn-sm">See all</a>
            </x-slot:actions>

            @forelse($upcoming as $appointment)
                <a href="{{ route('patient.appointments.show', $appointment) }}"
                   class="flex items-center gap-4 px-5 py-4 border-b border-pine/6 last:border-0 hover:bg-mint/12 transition">
                    <div class="w-12 text-center shrink-0">
                        <p class="font-mono text-[10px] uppercase text-pine/45">{{ $appointment->scheduled_on->format('M') }}</p>
                        <p class="font-display text-xl font-bold leading-none">{{ $appointment->scheduled_on->format('j') }}</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $appointment->service?->name ?? 'Consultation' }}</p>
                        <p class="text-xs text-pine/55 truncate">{{ $appointment->doctor->full_name }} · {{ $appointment->time_label }}</p>
                    </div>
                    <x-ui.status :status="$appointment->status" />
                </a>
            @empty
                <x-ui.empty title="Nothing booked" message="Your diary is clear.">
                    <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary btn-sm">Book a visit</a>
                </x-ui.empty>
            @endforelse
        </x-ui.panel>

        <div class="space-y-6">
            {{-- Recent records --}}
            <x-ui.panel title="Recent visits" :padded="false">
                <x-slot:actions>
                    <a href="{{ route('patient.records.index') }}" class="btn btn-ghost btn-sm">All records</a>
                </x-slot:actions>

                @forelse($records as $record)
                    <a href="{{ route('patient.records.show', $record) }}"
                       class="block px-5 py-3.5 border-b border-pine/6 last:border-0 hover:bg-mint/12 transition">
                        <p class="text-sm font-medium truncate">{{ $record->diagnosis ?? $record->chief_complaint ?? 'Clinical note' }}</p>
                        <p class="text-xs text-pine/55 mt-0.5">{{ $record->visited_on->format('j M Y') }} · {{ $record->doctor->full_name }}</p>
                    </a>
                @empty
                    <x-ui.empty title="No records yet" message="Notes appear here after your first visit." />
                @endforelse
            </x-ui.panel>

            {{-- Invoices --}}
            @if($invoices->isNotEmpty())
                <x-ui.panel title="Outstanding invoices" :padded="false">
                    @foreach($invoices as $invoice)
                        <a href="{{ route('patient.invoices.show', $invoice) }}"
                           class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-pine/6 last:border-0 hover:bg-mint/12 transition">
                            <div>
                                <p class="font-mono text-xs">{{ $invoice->number }}</p>
                                <p class="text-xs text-pine/55 mt-0.5">Due {{ $invoice->due_on?->format('j M Y') }}</p>
                            </div>
                            <span class="font-medium text-sm">{{ $currency }} {{ number_format($invoice->balance) }}</span>
                        </a>
                    @endforeach
                </x-ui.panel>
            @endif
        </div>
    </div>
</div>
@endsection
