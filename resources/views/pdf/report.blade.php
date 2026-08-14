<!DOCTYPE html>
<html><head><meta charset="utf-8">@include('pdf._styles')</head>
<body>
@include('pdf._header')
@php $currency = \App\Models\Setting::get('currency_symbol', 'Rs'); @endphp

<p class="doc-title">Practice report</p>
<p class="doc-meta">{{ $from->format('j F Y') }} – {{ $to->format('j F Y') }}</p>

<div class="panel" style="margin-top:14px;">
    <table class="grid">
        <tr>
            <td style="width:50%;">
                <div class="label">Appointments in period</div>
                <div class="value" style="font-size:20px;"><strong>{{ number_format($appointments) }}</strong></div>
            </td>
            <td>
                <div class="label">Payments collected</div>
                <div class="value" style="font-size:20px;"><strong>{{ $currency }} {{ number_format((float) $collected) }}</strong></div>
            </td>
        </tr>
    </table>
</div>

<h2 class="section">By treatment</h2>
<table class="items">
    <thead><tr><th>Treatment</th><th class="right">Bookings</th><th class="right">Value</th></tr></thead>
    <tbody>
        @forelse($byService as $row)
            <tr>
                <td>{{ $row->name }}</td>
                <td class="right">{{ $row->bookings }}</td>
                <td class="right">{{ $currency }} {{ number_format((float) $row->value) }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="text-align:center;padding:16px;color:#8AA39E;">No data in this period.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="foot">Generated {{ now()->format('j M Y, H:i') }} · Ivora Dental Studio · Commercially confidential</div>
</body></html>
