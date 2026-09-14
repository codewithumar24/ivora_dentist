@extends('layouts.console')
@section('heading', $patient->name)
@section('subheading', ($patient->patientProfile?->mrn ?? 'No MRN').' · registered '.$patient->created_at->format('j M Y'))

@section('actions')
    @permission('patients.update')
        <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-ghost btn-sm">Edit</a>
    @endpermission
    @permission('appointments.create')
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary btn-sm">Book</a>
    @endpermission
@endsection

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="grid lg:grid-cols-[320px_1fr] gap-6 items-start">

    <aside class="space-y-5 lg:sticky lg:top-24">
        <x-ui.panel>
            <div class="text-center">
                <img src="{{ $patient->avatar_url }}" alt="" class="w-20 h-20 rounded-3xl object-cover mx-auto mb-4">
                <p class="font-display text-lg font-semibold">{{ $patient->name }}</p>
                <p class="font-mono text-xs text-pine/50 mt-1">{{ $patient->patientProfile?->mrn }}</p>
                <span class="badge badge-{{ $patient->status === 'active' ? 'jade' : 'slate' }} mt-3">{{ ucfirst($patient->status) }}</span>
            </div>

            <dl class="mt-6 pt-5 border-t border-pine/8 space-y-3.5 text-sm">
                @foreach([
                    'Phone' => $patient->phone,
                    'Email' => $patient->email,
                    'Age' => $patient->age ? $patient->age.' years' : '—',
                    'Gender' => $patient->gender ? ucfirst($patient->gender) : '—',
                    'City' => $patient->city ?? '—',
                ] as $label => $value)
                    <div class="flex justify-between gap-3">
                        <dt class="text-pine/50">{{ $label }}</dt>
                        <dd class="text-right truncate">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.panel>

        @if($patient->patientProfile)
            <x-ui.panel title="Medical alerts">
                @if($patient->patientProfile->allergies)
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 mb-4">
                        <p class="eyebrow text-rose-700 mb-1">Allergies</p>
                        <p class="text-sm text-rose-900">{{ $patient->patientProfile->allergies }}</p>
                    </div>
                @endif

                <dl class="space-y-3 text-sm">
                    @foreach([
                        'Blood group' => $patient->patientProfile->blood_group,
                        'Conditions' => $patient->patientProfile->medical_conditions,
                        'Medications' => $patient->patientProfile->current_medications,
                        'Smoker' => $patient->patientProfile->is_smoker ? 'Yes' : 'No',
                        'Insurance' => $patient->patientProfile->insurance_provider,
                        'Emergency' => $patient->patientProfile->emergency_contact_name,
                    ] as $label => $value)
                        @if($value)
                            <div>
                                <dt class="eyebrow text-pine/45 mb-1">{{ $label }}</dt>
                                <dd class="text-pine/80">{{ $value }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </x-ui.panel>
        @endif
    </aside>

    <div class="space-y-5" data-tabs>
        <div class="flex flex-wrap gap-2">
            @foreach(['appointments' => 'Appointments', 'records' => 'Clinical notes', 'billing' => 'Billing'] as $key => $label)
                <button data-tab="{{ $key }}" class="btn btn-sm {{ $loop->first ? 'btn-primary is-active' : 'btn-ghost' }}">{{ $label }}</button>
            @endforeach
        </div>

        <div data-tab-panel="appointments">
            <x-ui.panel :padded="false">
                <table class="data-table">
                    <thead><tr><th>Reference</th><th>When</th><th>Practitioner</th><th>Treatment</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td class="font-mono text-xs">{{ $appointment->reference }}</td>
                                <td class="whitespace-nowrap">{{ $appointment->scheduled_on->format('j M Y') }}</td>
                                <td>{{ $appointment->doctor->full_name }}</td>
                                <td>{{ $appointment->service?->name ?? '—' }}</td>
                                <td><x-ui.status :status="$appointment->status" /></td>
                                <td class="text-right"><a href="{{ route('admin.appointments.show', $appointment) }}" class="btn btn-ghost btn-sm">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty title="Never booked" message="This patient has no appointments yet." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-ui.panel>
        </div>

        <div data-tab-panel="records" class="hidden space-y-4">
            @forelse($records as $record)
                <x-ui.panel>
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                        <div>
                            <p class="font-medium">{{ $record->diagnosis ?: $record->chief_complaint ?: 'Clinical note' }}</p>
                            <p class="text-xs text-pine/55 mt-1">{{ $record->visited_on->format('j M Y') }} · {{ $record->doctor->full_name }}</p>
                        </div>
                        @permission('records.view')
                            <a href="{{ route('admin.records.show', $record) }}" class="btn btn-ghost btn-sm">Open</a>
                        @endpermission
                    </div>
                    <p class="text-[0.95rem] text-pine/75 leading-relaxed">{{ Str::limit($record->treatment_performed, 200) }}</p>
                </x-ui.panel>
            @empty
                <x-ui.panel><x-ui.empty title="No clinical notes" message="Notes appear after treatment." /></x-ui.panel>
            @endforelse
        </div>

        <div data-tab-panel="billing" class="hidden">
            <x-ui.panel :padded="false">
                <table class="data-table">
                    <thead><tr><th>Invoice</th><th>Issued</th><th>Total</th><th>Paid</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="font-mono text-xs">{{ $invoice->number }}</td>
                                <td>{{ $invoice->issued_on->format('j M Y') }}</td>
                                <td class="font-mono">{{ $currency }} {{ number_format((float) $invoice->total) }}</td>
                                <td class="font-mono">{{ $currency }} {{ number_format((float) $invoice->amount_paid) }}</td>
                                <td><span class="badge badge-{{ $invoice->status === 'paid' ? 'jade' : ($invoice->status === 'partial' ? 'amber' : 'rose') }}">{{ ucfirst($invoice->status) }}</span></td>
                                <td class="text-right">
                                    @permission('billing.view')
                                        <a href="{{ route('admin.billing.show', $invoice) }}" class="btn btn-ghost btn-sm">Open</a>
                                    @endpermission
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty title="No invoices" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-ui.panel>
        </div>
    </div>
</div>
@endsection
