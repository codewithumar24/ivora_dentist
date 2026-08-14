@extends('layouts.console')
@section('heading', 'Working hours')
@section('subheading', $doctor->full_name)

@section('content')
<div class="grid lg:grid-cols-[1fr_340px] gap-6 items-start max-w-5xl">

    <x-ui.panel title="Weekly pattern" subtitle="Slots on the website are generated from this, minus breaks and time off.">
        <form action="{{ route('admin.doctors.schedule.save', $doctor) }}" method="POST" class="space-y-3">
            @csrf @method('PUT')

            @foreach($days as $index => $dayName)
                @php $schedule = $doctor->schedules->firstWhere('day_of_week', $index); @endphp

                <div class="p-4 rounded-2xl border border-pine/10 {{ $schedule ? 'bg-mint/15' : '' }}">
                    <label class="flex items-center gap-2.5 mb-3 cursor-pointer">
                        <input type="checkbox" name="days[{{ $index }}][active]" value="1" @checked($schedule)
                               class="w-4 h-4 rounded border-pine/25 text-pine focus:ring-jade">
                        <span class="font-medium text-sm">{{ $dayName }}</span>
                    </label>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                        @foreach([
                            'starts_at' => ['Opens', $schedule?->starts_at ?? '09:00'],
                            'ends_at' => ['Closes', $schedule?->ends_at ?? '17:00'],
                            'break_starts_at' => ['Break from', $schedule?->break_starts_at ?? '13:00'],
                            'break_ends_at' => ['Break to', $schedule?->break_ends_at ?? '14:00'],
                        ] as $field => [$label, $value])
                            <div>
                                <label class="field-label !text-[11px]">{{ $label }}</label>
                                <input type="time" name="days[{{ $index }}][{{ $field }}]" value="{{ substr((string) $value, 0, 5) }}" class="field !py-1.5 !text-sm">
                            </div>
                        @endforeach
                        <div>
                            <label class="field-label !text-[11px]">Slot (min)</label>
                            <input type="number" name="days[{{ $index }}][slot_minutes]" min="10" max="120" step="5"
                                   value="{{ $schedule?->slot_minutes ?? config('clinic.slot_minutes') }}" class="field !py-1.5 !text-sm">
                        </div>
                    </div>
                </div>
            @endforeach

            <button class="btn btn-primary mt-4">Save working hours</button>
        </form>
    </x-ui.panel>

    <aside class="space-y-5">
        <x-ui.panel title="Block out time" subtitle="Holidays, conferences, surgery days.">
            <form action="{{ route('admin.doctors.timeOff.store', $doctor) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <x-ui.field label="From" name="starts_on" type="date" required />
                    <x-ui.field label="To" name="ends_on" type="date" required />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-ui.field label="Start time" name="starts_at" type="time" hint="Leave blank for all day." />
                    <x-ui.field label="End time" name="ends_at" type="time" />
                </div>
                <x-ui.field label="Reason" name="reason" />
                <button class="btn btn-primary btn-sm w-full">Block it out</button>
            </form>
        </x-ui.panel>

        @if($doctor->timeOffs->isNotEmpty())
            <x-ui.panel title="Currently blocked" :padded="false">
                @foreach($doctor->timeOffs->sortBy('starts_on') as $timeOff)
                    <div class="flex items-center justify-between gap-3 px-5 py-3 border-b border-pine/6 last:border-0">
                        <div class="min-w-0">
                            <p class="text-sm">{{ $timeOff->starts_on->format('j M') }} – {{ $timeOff->ends_on->format('j M Y') }}</p>
                            <p class="text-xs text-pine/55 truncate">
                                {{ $timeOff->isFullDay() ? 'All day' : substr($timeOff->starts_at, 0, 5).'–'.substr($timeOff->ends_at ?? '', 0, 5) }}
                                @if($timeOff->reason) · {{ $timeOff->reason }} @endif
                            </p>
                        </div>
                        <form action="{{ route('admin.doctors.timeOff.destroy', [$doctor, $timeOff]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-xs text-rose-600 hover:underline">Remove</button>
                        </form>
                    </div>
                @endforeach
            </x-ui.panel>
        @endif
    </aside>
</div>
@endsection
