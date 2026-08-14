<!DOCTYPE html>
<html><head><meta charset="utf-8">@include('pdf._styles')</head>
<body>
@include('pdf._header')

<p class="doc-title">Chair list — {{ $date->format('l j F Y') }}</p>
<p class="doc-meta">{{ $appointments->count() }} appointment(s)</p>

<table class="items" style="margin-top:14px;">
    <thead>
        <tr>
            <th style="width:11%;">Time</th>
            <th style="width:22%;">Patient</th>
            <th style="width:13%;">Phone</th>
            <th style="width:18%;">Practitioner</th>
            <th style="width:18%;">Treatment</th>
            <th>Notes / allergies</th>
        </tr>
    </thead>
    <tbody>
        @forelse($appointments as $appointment)
            <tr>
                <td><strong>{{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('g:i A') }}</strong></td>
                <td>
                    {{ $appointment->patient->name }}
                    <div style="font-size:8.5px;color:#8AA39E;">{{ $appointment->patient->patientProfile?->mrn }}</div>
                </td>
                <td>{{ $appointment->patient->phone }}</td>
                <td>{{ $appointment->doctor->full_name }}</td>
                <td>{{ $appointment->service?->name ?? 'Consultation' }}</td>
                <td>
                    @if($appointment->patient->patientProfile?->allergies)
                        <strong style="color:#A03027;">Allergy: {{ $appointment->patient->patientProfile->allergies }}</strong><br>
                    @endif
                    {{ \Illuminate\Support\Str::limit($appointment->symptoms ?: $appointment->reason, 60) }}
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;padding:20px;color:#8AA39E;">Nothing booked.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="foot">Printed {{ now()->format('j M Y, H:i') }} · Confidential — do not leave at reception</div>
</body></html>
