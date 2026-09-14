@extends('layouts.console')
@section('heading', $appointment->reference)
@section('subheading', $appointment->scheduled_on->format('l j F Y').' · '.$appointment->time_label)

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="grid lg:grid-cols-[1fr_330px] gap-6 items-start max-w-6xl">

    <div class="space-y-5">
        <x-ui.panel title="Patient">
            <div class="flex flex-wrap items-center gap-5">
                <img src="{{ $appointment->patient->avatar_url }}" alt="" class="w-16 h-16 rounded-2xl object-cover">
                <div class="flex-1 min-w-[200px]">
                    <p class="font-display text-lg font-semibold">{{ $appointment->patient->name }}</p>
                    <p class="text-sm text-pine/70">
                        {{ $appointment->patient->patientProfile?->mrn }} ·
                        {{ $appointment->patient->age ? $appointment->patient->age.' yrs' : 'DOB unknown' }} ·
                        {{ $appointment->patient->phone }}
                    </p>
                </div>
                @permission('patients.view')
                    <a href="{{ route('admin.patients.show', $appointment->patient) }}" class="btn btn-ghost btn-sm">Full record</a>
                @endpermission
            </div>

            @if($appointment->patient->patientProfile?->allergies)
                <div class="mt-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200">
                    <p class="eyebrow text-rose-700 mb-1">Allergies</p>
                    <p class="text-sm text-rose-900">{{ $appointment->patient->patientProfile->allergies }}</p>
                </div>
            @endif
        </x-ui.panel>

        <x-ui.panel title="Appointment">
            <dl class="grid sm:grid-cols-3 gap-5 text-sm">
                @foreach([
                    'Practitioner' => $appointment->doctor->full_name,
                    'Treatment' => $appointment->service?->name ?? '—',
                    'Visit type' => Str::headline($appointment->visit_type),
                    'Booked via' => Str::headline($appointment->source),
                    'Fee' => $currency.' '.number_format((float) $appointment->fee),
                    'Payment' => Str::headline($appointment->payment_status),
                ] as $label => $value)
                    <div>
                        <dt class="eyebrow text-pine/45 mb-1.5">{{ $label }}</dt>
                        <dd class="font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            @if($appointment->symptoms || $appointment->reason)
                <div class="mt-5 pt-5 border-t border-pine/8">
                    <p class="eyebrow text-pine/45 mb-2">Patient's words</p>
                    <p class="text-sm text-pine/80 leading-relaxed">{{ $appointment->symptoms ?: $appointment->reason }}</p>
                </div>
            @endif
        </x-ui.panel>

        @permission('appointments.update')
            <x-ui.panel title="Front desk notes">
                <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST" class="space-y-4">
                    @csrf @method('PATCH')
                    <x-ui.field name="staff_notes" type="textarea" :value="$appointment->staff_notes"
                                hint="Internal only — never shown to the patient." />
                    <div class="grid sm:grid-cols-2 gap-4">
                        <x-ui.field label="Fee" name="fee" type="number" step="0.01" :value="$appointment->fee" />
                        <div>
                            <label class="field-label" for="payment_status">Payment status</label>
                            <select name="payment_status" id="payment_status" class="field">
                                @foreach(['unpaid', 'partial', 'paid', 'refunded'] as $status)
                                    <option value="{{ $status }}" @selected($appointment->payment_status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm">Save notes</button>
                </form>
            </x-ui.panel>
        @endpermission

        @if($appointment->medicalRecord)
            <x-ui.panel title="Clinical note">
                <x-slot:actions>
                    @permission('records.view')
                        <a href="{{ route('admin.records.show', $appointment->medicalRecord) }}" class="btn btn-ghost btn-sm">Open</a>
                    @endpermission
                </x-slot:actions>
                <dl class="space-y-4 text-sm">
                    @foreach(['Diagnosis' => $appointment->medicalRecord->diagnosis, 'Treatment' => $appointment->medicalRecord->treatment_performed] as $label => $value)
                        @if($value)
                            <div>
                                <dt class="eyebrow text-pine/45 mb-1.5">{{ $label }}</dt>
                                <dd class="text-pine/80">{{ $value }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </x-ui.panel>
        @elseif($appointment->status === 'completed')
            @permission('records.create')
                <div class="card-flat p-6 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-sm text-pine/70">This visit has no clinical note yet.</p>
                    <a href="{{ route('admin.records.create', ['appointment' => $appointment->id]) }}" class="btn btn-primary btn-sm">Write one</a>
                </div>
            @endpermission
        @endif
    </div>

    <aside class="space-y-5 lg:sticky lg:top-24">
        <x-ui.panel title="Status">
            <x-ui.status :status="$appointment->status" class="mb-4" />

            @permission('appointments.update')
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['confirmed' => 'Confirm', 'completed' => 'Complete', 'no_show' => 'No show', 'cancelled' => 'Cancel'] as $status => $label)
                        <form action="{{ route('admin.appointments.status', $appointment) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $status }}">
                            <button class="btn btn-sm w-full {{ $status === 'cancelled' ? 'border border-rose-200 text-rose-700 hover:bg-rose-50' : 'btn-ghost' }}"
                                    {{ $appointment->status === $status ? 'disabled' : '' }}>{{ $label }}</button>
                        </form>
                    @endforeach
                </div>

                <form action="{{ route('admin.appointments.reschedule', $appointment) }}" method="POST" class="mt-5 pt-5 border-t border-pine/8 space-y-3">
                    @csrf @method('PATCH')
                    <p class="eyebrow text-pine/45">Move it</p>
                    <x-ui.field name="scheduled_on" type="date" :value="$appointment->scheduled_on->toDateString()" required />
                    <x-ui.field name="starts_at" type="time" :value="substr($appointment->starts_at, 0, 5)" required />
                    <button class="btn btn-ghost btn-sm w-full">Reschedule</button>
                </form>
            @endpermission
        </x-ui.panel>

        @if($appointment->invoice)
            <x-ui.panel title="Invoice">
                <p class="font-mono text-sm">{{ $appointment->invoice->number }}</p>
                <p class="font-display text-2xl font-bold mt-2">{{ $currency }} {{ number_format((float) $appointment->invoice->total) }}</p>
                <p class="text-xs text-pine/55 mt-1">Balance {{ $currency }} {{ number_format($appointment->invoice->balance) }}</p>
                @permission('billing.view')
                    <a href="{{ route('admin.billing.show', $appointment->invoice) }}" class="btn btn-ghost btn-sm w-full mt-4">Open invoice</a>
                @endpermission
            </x-ui.panel>
        @endif

        @permission('appointments.delete')
            <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST"
                  data-confirm="Delete this appointment permanently?">
                @csrf @method('DELETE')
                <button class="btn btn-sm w-full border border-rose-200 text-rose-700 hover:bg-rose-50">Delete appointment</button>
            </form>
        @endpermission
    </aside>
</div>
@endsection
