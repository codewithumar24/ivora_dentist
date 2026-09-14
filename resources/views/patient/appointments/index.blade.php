@extends('layouts.portal')
@section('heading', 'Appointments')
@section('subheading', 'Everything past and upcoming')

@section('content')
<div class="space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('patient.appointments.index') }}" class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-ghost' }}">
                All
            </a>
            @foreach(config('clinic.appointment_statuses') as $key => $meta)
                <a href="{{ route('patient.appointments.index', ['status' => $key]) }}"
                   class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-ghost' }}">{{ $meta['label'] }}</a>
            @endforeach
        </div>
        <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary btn-sm">Book a visit</a>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <x-ui.stat label="Upcoming" :value="$counts['upcoming']" tone="jade" count icon="M4 5h12v12H4zM7 3v4M13 3v4" />
        <x-ui.stat label="Completed" :value="$counts['completed']" tone="pine" count icon="M5 10.5 8.5 14 15 6" />
        <x-ui.stat label="Cancelled" :value="$counts['cancelled']" tone="rose" count icon="M6 6l8 8M14 6l-8 8" />
    </div>

    <x-ui.panel :padded="false">
        @forelse($appointments as $appointment)
            <div class="flex flex-wrap items-center gap-4 px-5 py-4 border-b border-pine/6 last:border-0 hover:bg-mint/10 transition">
                <div class="w-14 text-center shrink-0">
                    <p class="font-mono text-[10px] uppercase text-pine/45">{{ $appointment->scheduled_on->format('M') }}</p>
                    <p class="font-display text-2xl font-bold leading-none">{{ $appointment->scheduled_on->format('j') }}</p>
                    <p class="font-mono text-[10px] text-pine/45 mt-0.5">{{ $appointment->scheduled_on->format('Y') }}</p>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <p class="font-medium">{{ $appointment->service?->name ?? 'Consultation' }}</p>
                    <p class="text-sm text-pine/70 mt-0.5">
                        {{ $appointment->doctor->full_name }} · {{ $appointment->time_label }}
                    </p>
                    <p class="font-mono text-xs text-pine/40 mt-1">{{ $appointment->reference }}</p>
                </div>

                <x-ui.status :status="$appointment->status" />

                <a href="{{ route('patient.appointments.show', $appointment) }}" class="btn btn-ghost btn-sm">Open</a>
            </div>
        @empty
            <x-ui.empty title="No appointments" message="Once you book, everything shows up here.">
                <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary btn-sm">Book a visit</a>
            </x-ui.empty>
        @endforelse
    </x-ui.panel>

    {{ $appointments->links() }}
</div>
@endsection
