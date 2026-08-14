<!DOCTYPE html>
<html><head><meta charset="utf-8">@include('pdf._styles')</head>
<body>
@include('pdf._header')

<p class="doc-title">Appointments export</p>
<p class="doc-meta">
    {{ $appointments->count() }} record(s)
    @if(!empty($filters['from'])) · from {{ \Illuminate\Support\Carbon::parse($filters['from'])->format('j M Y') }} @endif
    @if(!empty($filters['to'])) · to {{ \Illuminate\Support\Carbon::parse($filters['to'])->format('j M Y') }} @endif
    @if(!empty($filters['status'])) · status: {{ $filters['status'] }} @endif
</p>

<table class="items" style="margin-top:14px;">
    <thead>
        <tr>
            <th>Reference</th><th>Date</th><th>Time</th><th>Patient</th>
            <th>Practitioner</th><th>Treatment</th><th>Status</th><th class="right">Fee</th>
        </tr>
    </thead>
    <tbody>
        @foreach($appointments as $appointment)
            <tr>
                <td>{{ $appointment->reference }}</td>
                <td>{{ $appointment->scheduled_on->format('j M Y') }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($appointment->starts_at)->format('g:i A') }}</td>
                <td>{{ $appointment->patient?->name }}</td>
                <td>{{ $appointment->doctor?->full_name }}</td>
                <td>{{ $appointment->service?->name ?? '—' }}</td>
                <td>{{ $appointment->status_label }}</td>
                <td class="right">{{ number_format((float) $appointment->fee) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="foot">Generated {{ now()->format('j M Y, H:i') }} · Ivora Dental Studio</div>
</body></html>
