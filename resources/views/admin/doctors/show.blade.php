@extends('layouts.console')
@section('heading', $doctor->full_name)
@section('subheading', $doctor->designation.' · '.($doctor->speciality?->name ?? 'General dentistry'))

@section('actions')
    @permission('schedules.manage')
        <a href="{{ route('admin.doctors.schedule', $doctor) }}" class="btn btn-ghost btn-sm">Working hours</a>
    @endpermission
    @permission('doctors.manage')
        <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-primary btn-sm">Edit</a>
    @endpermission
@endsection

@section('content')
<div class="grid lg:grid-cols-[320px_1fr] gap-6 items-start">
    <aside class="space-y-5">
        <x-ui.panel>
            <div class="text-center">
                <img src="{{ $doctor->user->avatar_url }}" alt="" class="w-20 h-20 rounded-3xl object-cover mx-auto mb-4">
                <p class="font-display text-lg font-semibold">{{ $doctor->full_name }}</p>
                <p class="text-sm text-pine/55 mt-1">{{ $doctor->qualifications }}</p>
            </div>

            <dl class="mt-6 pt-5 border-t border-pine/8 space-y-3 text-sm">
                @foreach([
                    'Email' => $doctor->user->email,
                    'Phone' => $doctor->user->phone,
                    'Licence' => $doctor->license_no,
                    'Experience' => $doctor->experience_years.' years',
                    'Fee' => ($settings['currency_symbol'] ?? 'Rs').' '.number_format((float) $doctor->consultation_fee),
                    'Rating' => number_format((float) $doctor->rating, 1).' ('.$doctor->reviews_count.')',
                ] as $label => $value)
                    <div class="flex justify-between gap-3">
                        <dt class="text-pine/50">{{ $label }}</dt>
                        <dd class="text-right truncate">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.panel>

        <x-ui.panel title="Weekly hours">
            @forelse($doctor->schedules->sortBy('day_of_week') as $schedule)
                <div class="flex justify-between text-sm py-1.5">
                    <span>{{ $schedule->day_name }}</span>
                    <span class="font-mono text-xs text-pine/60">{{ $schedule->window }}</span>
                </div>
            @empty
                <p class="text-sm text-pine/55">No hours set — this practitioner is not bookable.</p>
            @endforelse
        </x-ui.panel>
    </aside>

    <div class="space-y-5">
        <x-ui.panel title="Biography">
            <p class="text-sm text-pine/80 leading-relaxed">{{ $doctor->biography }}</p>

            @if($doctor->services->isNotEmpty())
                <div class="mt-5 pt-5 border-t border-pine/8">
                    <p class="eyebrow text-pine/45 mb-3">Treatments offered</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($doctor->services as $service)
                            <span class="badge badge-mint">{{ $service->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-ui.panel>

        <x-ui.panel title="Recent appointments" :padded="false">
            <table class="data-table">
                <thead><tr><th>Date</th><th>Patient</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td class="whitespace-nowrap">{{ $appointment->scheduled_on->format('j M Y') }}</td>
                            <td>{{ $appointment->patient->name }}</td>
                            <td><x-ui.status :status="$appointment->status" /></td>
                            <td class="text-right"><a href="{{ route('admin.appointments.show', $appointment) }}" class="btn btn-ghost btn-sm">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-ui.empty title="No appointments yet" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.panel>

        @if($doctor->timeOffs->isNotEmpty())
            <x-ui.panel title="Booked time off">
                <ul class="space-y-2 text-sm">
                    @foreach($doctor->timeOffs as $timeOff)
                        <li class="flex justify-between gap-3 py-1.5 border-b border-pine/6 last:border-0">
                            <span>{{ $timeOff->starts_on->format('j M') }} – {{ $timeOff->ends_on->format('j M Y') }}</span>
                            <span class="text-pine/55">{{ $timeOff->reason }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-ui.panel>
        @endif
    </div>
</div>
@endsection
