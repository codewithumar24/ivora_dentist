<!DOCTYPE html>
<html><head><meta charset="utf-8">@include('pdf._styles')</head>
<body>
@include('pdf._header')
@php $currency = \App\Models\Setting::get('currency_symbol', 'Rs'); @endphp

<table class="grid" style="margin-bottom:16px;">
    <tr>
        <td style="width:55%;">
            <p class="doc-title">Invoice {{ $invoice->number }}</p>
            <p class="doc-meta">
                Issued {{ $invoice->issued_on->format('j F Y') }} ·
                Due {{ $invoice->due_on?->format('j F Y') ?? 'on receipt' }}
            </p>
            <p style="margin-top:6px;"><span class="badge">{{ ucfirst($invoice->status) }}</span></p>
        </td>
        <td class="right">
            <div class="label">Billed to</div>
            <div class="value"><strong>{{ $invoice->patient->name }}</strong></div>
            <div class="doc-meta">
                {{ $invoice->patient->patientProfile?->mrn }}<br>
                {{ $invoice->patient->email }}<br>
                {{ $invoice->patient->phone }}
            </div>
        </td>
    </tr>
</table>

@if($invoice->doctor)
    <div class="panel">
        <span class="label">Treating clinician</span>
        <span class="value">{{ $invoice->doctor->full_name }}</span>
    </div>
@endif

<table class="items">
    <thead>
        <tr>
            <th style="width:52%;">Description</th>
            <th class="right">Qty</th>
            <th class="right">Unit price</th>
            <th class="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ $currency }} {{ number_format((float) $item->unit_price, 2) }}</td>
                <td class="right">{{ $currency }} {{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="totals" style="width:48%; margin-left:52%; margin-top:12px;">
    <tr><td>Subtotal</td><td class="right">{{ $currency }} {{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
    @if((float) $invoice->discount)
        <tr><td>Discount</td><td class="right">− {{ $currency }} {{ number_format((float) $invoice->discount, 2) }}</td></tr>
    @endif
    @if((float) $invoice->tax)
        <tr><td>Tax</td><td class="right">{{ $currency }} {{ number_format((float) $invoice->tax, 2) }}</td></tr>
    @endif
    <tr><td class="grand">Total</td><td class="right grand">{{ $currency }} {{ number_format((float) $invoice->total, 2) }}</td></tr>
    <tr><td>Paid</td><td class="right">{{ $currency }} {{ number_format((float) $invoice->amount_paid, 2) }}</td></tr>
    @if($invoice->balance > 0)
        <tr><td><strong>Balance due</strong></td><td class="right"><strong>{{ $currency }} {{ number_format($invoice->balance, 2) }}</strong></td></tr>
    @endif
</table>

@if($invoice->payments->isNotEmpty())
    <h2 class="section">Payments received</h2>
    <table class="items">
        <thead><tr><th>Receipt</th><th>Date</th><th>Method</th><th class="right">Amount</th></tr></thead>
        <tbody>
            @foreach($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->reference }}</td>
                    <td>{{ $payment->paid_at->format('j M Y') }}</td>
                    <td>{{ \Illuminate\Support\Str::headline($payment->method) }}</td>
                    <td class="right">{{ $currency }} {{ number_format((float) $payment->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if($invoice->notes)
    <h2 class="section">Notes</h2>
    <p>{{ $invoice->notes }}</p>
@endif

<div class="foot">
    Ivora Dental Studio · {{ \App\Models\Setting::get('address') }} · Generated {{ now()->format('j M Y, H:i') }}
</div>
</body></html>
