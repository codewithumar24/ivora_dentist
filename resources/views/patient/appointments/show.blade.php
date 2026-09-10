@extends('layouts.portal')
@section('heading', 'Appointment '.$appointment->reference)
@section('subheading', $appointment->scheduled_on->format('l j F Y').' · '.$appointment->time_label)

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start max-w-5xl">

    <div class="space-y-5">
        <div class="on-dark rounded-3xl relative overflow-hidden p-7">
            <div class="grain absolute inset-0"></div>
            <div class="relative flex flex-wrap items-start justify-between gap-6">
                <div>
                    <x-ui.status :status="$appointment->status" class="mb-4" />
                    <p class="font-display text-3xl font-bold text-porcelain leading-tight">
                        {{ $appointment->scheduled_on->format('l j F') }}<br>{{ $appointment->starts_at_carbon->format('g:i A') }}
                    </p>
                    <p class="text-porcelain/80 mt-3">{{ $appointment->service?->name ?? 'Consultation' }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <img src="{{ $appointment->doctor->user->avatar_url }}" alt="" class="w-12 h-12 rounded-2xl object-cover">
                    <div>
                        <p class="text-sm text-porcelain font-medium">{{ $appointment->doctor->full_name }}</p>
                        <p class="text-xs text-mint/70">{{ $appointment->doctor->speciality?->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <x-ui.panel title="Details">
            <dl class="grid sm:grid-cols-2 gap-5 text-sm">
                @foreach([
                    'Reference' => $appointment->reference,
                    'Visit type' => Str::headline($appointment->visit_type),
                    'Duration' => $appointment->time_label,
                    'Estimated fee' => $currency.' '.number_format((float) $appointment->fee),
                    'Booked' => $appointment->created_at->format('j M Y'),
                    'Payment' => Str::headline($appointment->payment_status),
                ] as $label => $value)
                    <div>
                        <dt class="eyebrow text-pine/45 mb-1.5">{{ $label }}</dt>
                        <dd class="font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            @if($appointment->reason || $appointment->symptoms)
                <div class="mt-6 pt-5 border-t border-pine/8">
                    <p class="eyebrow text-pine/45 mb-2">What you told us</p>
                    <p class="text-[0.95rem] text-pine/75 leading-relaxed">{{ $appointment->symptoms ?: $appointment->reason }}</p>
                </div>
            @endif
        </x-ui.panel>

        @if($appointment->medicalRecord)
            <x-ui.panel title="Clinical note from this visit">
                <dl class="space-y-4 text-sm">
                    @foreach(['Diagnosis' => $appointment->medicalRecord->diagnosis, 'Treatment' => $appointment->medicalRecord->treatment_performed, 'Advice' => $appointment->medicalRecord->advice] as $label => $value)
                        @if($value)
                            <div>
                                <dt class="eyebrow text-pine/45 mb-1.5">{{ $label }}</dt>
                                <dd class="text-pine/80 leading-relaxed">{{ $value }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>

                @if($appointment->medicalRecord->prescription)
                    <a href="{{ route('patient.prescriptions.pdf', $appointment->medicalRecord->prescription) }}" class="btn btn-ghost btn-sm mt-5">
                        Download prescription
                    </a>
                @endif
            </x-ui.panel>
        @endif

        @if($appointment->status === 'completed')
            <x-ui.panel title="How was it?" subtitle="Feedback goes to the practice manager before it goes anywhere else.">
                <form action="{{ route('patient.appointments.review', $appointment) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="field-label">Rating</label>
                        <div class="flex gap-2">
                            @for($i = 5; $i >= 1; $i--)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" required>
                                    <span class="w-10 h-10 grid place-items-center rounded-xl border border-pine/12 peer-checked:bg-pine peer-checked:text-mint transition">{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>
                    <x-ui.field label="Anything to add?" name="body" type="textarea" />
                    <button class="btn btn-primary btn-sm">Send feedback</button>
                </form>
            </x-ui.panel>
        @endif
    </div>

    <aside class="space-y-5 lg:sticky lg:top-24">
        @if($appointment->isCancellableByPatient())
            <x-ui.panel title="Change of plan?">
                <form action="{{ route('patient.appointments.reschedule', $appointment) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <x-ui.field label="New date" name="scheduled_on" type="date" :value="$appointment->scheduled_on->toDateString()" required />
                    <x-ui.field label="New time" name="starts_at" type="time" :value="substr($appointment->starts_at, 0, 5)" required />
                    <button class="btn btn-ghost btn-sm w-full">Move appointment</button>
                </form>

                <form action="{{ route('patient.appointments.cancel', $appointment) }}" method="POST"
                      data-confirm="Cancel this appointment? The slot goes back into the diary straight away."
                      class="mt-4 pt-4 border-t border-pine/8 space-y-3">
                    @csrf @method('DELETE')
                    <x-ui.field label="Reason (optional)" name="cancel_reason" />
                    <button class="btn btn-sm w-full border border-rose-200 text-rose-700 hover:bg-rose-50">Cancel appointment</button>
                </form>
            </x-ui.panel>
        @else
            <x-ui.panel title="Need to change it?">
                <p class="text-[0.95rem] text-pine/75 leading-relaxed">
                    Online changes close {{ config('clinic.cancel_window_hours') }} hours before the visit.
                    Call reception and we will sort it out.
                </p>
                <a href="tel:{{ $settings['phone'] ?? '' }}" class="btn btn-primary btn-sm w-full mt-4">{{ $settings['phone'] ?? 'Call us' }}</a>
            </x-ui.panel>
        @endif

        @if($appointment->invoice)
            <x-ui.panel title="Invoice">
                <p class="font-mono text-sm">{{ $appointment->invoice->number }}</p>
                <p class="text-2xl font-display font-bold mt-2">{{ $currency }} {{ number_format((float) $appointment->invoice->total) }}</p>
                <a href="{{ route('patient.invoices.show', $appointment->invoice) }}" class="btn btn-ghost btn-sm w-full mt-4">View invoice</a>
            </x-ui.panel>
        @endif

        <x-ui.panel title="Getting here">
            <p class="text-[0.95rem] text-pine/75 leading-relaxed">{{ $settings['address'] ?? '' }}</p>
            <p class="text-xs text-pine/55 mt-3">Please arrive ten minutes early if this is a first visit.</p>
        </x-ui.panel>
    </aside>
</div>
@endsection
