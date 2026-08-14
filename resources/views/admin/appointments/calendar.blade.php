@extends('layouts.console')
@section('heading', 'Diary')
@section('subheading', 'Week of '.$weekStart->format('j F Y'))

@section('actions')
    <a href="{{ route('admin.appointments.calendar', ['date' => $date->copy()->subWeek()->toDateString()]) }}" class="btn btn-ghost btn-sm">← Previous</a>
    <a href="{{ route('admin.appointments.calendar') }}" class="btn btn-ghost btn-sm">This week</a>
    <a href="{{ route('admin.appointments.calendar', ['date' => $date->copy()->addWeek()->toDateString()]) }}" class="btn btn-ghost btn-sm">Next →</a>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 xl:grid-cols-7 gap-3">
    @for($i = 0; $i < 7; $i++)
        @php
            $day = $weekStart->copy()->addDays($i);
            $list = $appointments[$day->toDateString()] ?? collect();
        @endphp

        <div class="card-flat overflow-hidden {{ $day->isToday() ? 'ring-2 ring-jade' : '' }}">
            <div class="px-4 py-3 border-b border-pine/8 {{ $day->isToday() ? 'bg-mint/40' : '' }}">
                <p class="font-mono text-[10px] uppercase tracking-widest text-pine/50">{{ $day->format('D') }}</p>
                <p class="font-display text-xl font-bold leading-none mt-1">{{ $day->format('j') }}</p>
                <p class="text-[11px] text-pine/50 mt-1">{{ $list->count() }} booked</p>
            </div>

            <div class="p-2 space-y-1.5 max-h-[540px] overflow-y-auto">
                @forelse($list as $appointment)
                    <a href="{{ route('admin.appointments.show', $appointment) }}"
                       class="block p-2.5 rounded-xl bg-porcelain hover:bg-mint/35 transition border-l-[3px] border-l-{{ $appointment->status_tone === 'jade' ? 'jade' : 'pine' }}"
                       style="border-left-color: {{ ['jade' => '#1C7A6E', 'pine' => '#10403B', 'amber' => '#E8B33C', 'rose' => '#D9736A', 'slate' => '#9BB0AC'][$appointment->status_tone] ?? '#10403B' }}">
                        <p class="font-mono text-[11px] text-pine/60">{{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('g:i A') }}</p>
                        <p class="text-xs font-medium truncate mt-0.5">{{ $appointment->patient->name }}</p>
                        <p class="text-[11px] text-pine/50 truncate">{{ Str::before($appointment->doctor->full_name, ' ') }} {{ Str::after($appointment->doctor->full_name, ' ') }}</p>
                    </a>
                @empty
                    <p class="text-[11px] text-pine/35 text-center py-6">Empty</p>
                @endforelse
            </div>
        </div>
    @endfor
</div>
@endsection
