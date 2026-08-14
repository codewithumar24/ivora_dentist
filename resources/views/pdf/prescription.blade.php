<!DOCTYPE html>
<html><head><meta charset="utf-8">@include('pdf._styles')</head>
<body>
@include('pdf._header')

<table class="grid" style="margin-bottom:16px;">
    <tr>
        <td style="width:55%;">
            <p class="doc-title">Prescription</p>
            <p class="doc-meta">{{ $prescription->reference }} · issued {{ $prescription->issued_on->format('j F Y') }}</p>
        </td>
        <td class="right">
            <div class="label">Prescriber</div>
            <div class="value"><strong>{{ $prescription->doctor->full_name }}</strong></div>
            <div class="doc-meta">
                {{ $prescription->doctor->qualifications }}<br>
                Licence {{ $prescription->doctor->license_no }}
            </div>
        </td>
    </tr>
</table>

<div class="panel">
    <table class="grid">
        <tr>
            <td style="width:33%;"><div class="label">Patient</div><div class="value">{{ $prescription->patient->name }}</div></td>
            <td style="width:33%;"><div class="label">MRN</div><div class="value">{{ $prescription->patient->patientProfile?->mrn ?? '—' }}</div></td>
            <td><div class="label">Age / sex</div><div class="value">{{ $prescription->patient->age ?? '—' }} / {{ $prescription->patient->gender ? ucfirst($prescription->patient->gender) : '—' }}</div></td>
        </tr>
    </table>
    @if($prescription->patient->patientProfile?->allergies)
        <div style="margin-top:8px;">
            <span class="label">Allergies</span>
            <span class="value" style="color:#A03027;"><strong>{{ $prescription->patient->patientProfile->allergies }}</strong></span>
        </div>
    @endif
</div>

<h2 class="section">℞</h2>

<table class="items">
    <thead>
        <tr>
            <th style="width:30%;">Medicine</th>
            <th>Dose</th>
            <th>Frequency</th>
            <th>Duration</th>
            <th>Instruction</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prescription->items as $item)
            <tr>
                <td><strong>{{ $item->medicine }}</strong></td>
                <td>{{ $item->dosage }}</td>
                <td>{{ $item->frequency }}</td>
                <td>{{ $item->duration }}</td>
                <td>{{ $item->instruction }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($prescription->notes)
    <h2 class="section">Notes</h2>
    <p>{{ $prescription->notes }}</p>
@endif

<table class="grid" style="margin-top:44px;">
    <tr>
        <td style="width:60%;"></td>
        <td style="border-top:1px solid #0A1614; padding-top:5px; text-align:center;">
            <div class="value">{{ $prescription->doctor->full_name }}</div>
            <div class="doc-meta">Signature</div>
        </td>
    </tr>
</table>

<div class="foot">
    This prescription is valid for 30 days from the date of issue. Ivora Dental Studio · {{ \App\Models\Setting::get('phone') }}
</div>
</body></html>
