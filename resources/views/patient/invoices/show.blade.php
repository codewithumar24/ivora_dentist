@extends('layouts.portal')
@section('heading', 'Invoice '.$invoice->number)
@section('subheading', 'Issued '.$invoice->issued_on->format('j F Y'))

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="max-w-3xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <span class="badge badge-{{ match($invoice->status) { 'paid' => 'jade', 'partial' => 'amber', 'void' => 'slate', default => 'rose' } }}">
            {{ ucfirst($invoice->status) }}
        </span>
        <a href="{{ route('patient.invoices.pdf', $invoice) }}" class="btn btn-primary btn-sm">Download PDF</a>
    </div>

    <x-ui.panel :padded="false">
        <div class="p-6 border-b border-pine/8 grid sm:grid-cols-2 gap-5 text-sm">
            <div>
                <p class="eyebrow text-pine/45 mb-1.5">Billed to</p>
                <p class="font-medium">{{ $invoice->patient->name }}</p>
                <p class="text-pine/70">{{ $invoice->patient->email }}</p>
            </div>
            <div class="sm:text-right">
                <p class="eyebrow text-pine/45 mb-1.5">Due</p>
                <p class="font-medium">{{ $invoice->due_on?->format('j F Y') ?? 'On receipt' }}</p>
                @if($invoice->doctor)<p class="text-pine/70">{{ $invoice->doctor->full_name }}</p>@endif
            </div>
        </div>

        <table class="data-table">
            <thead><tr><th>Description</th><th class="text-right">Qty</th><th class="text-right">Unit</th><th class="text-right">Total</th></tr></thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-right font-mono">{{ $item->quantity }}</td>
                        <td class="text-right font-mono">{{ number_format((float) $item->unit_price) }}</td>
                        <td class="text-right font-mono">{{ number_format((float) $item->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-6 space-y-2 text-sm">
            @foreach([
                'Subtotal' => $invoice->subtotal,
                'Discount' => $invoice->discount,
                'Tax' => $invoice->tax,
            ] as $label => $value)
                @if((float) $value != 0 || $label === 'Subtotal')
                    <div class="flex justify-between">
                        <span class="text-pine/70">{{ $label }}</span>
                        <span class="font-mono">{{ $label === 'Discount' ? '−' : '' }}{{ $currency }} {{ number_format((float) $value) }}</span>
                    </div>
                @endif
            @endforeach

            <div class="flex justify-between pt-3 mt-3 border-t border-pine/10 text-base font-semibold">
                <span>Total</span>
                <span class="font-mono">{{ $currency }} {{ number_format((float) $invoice->total) }}</span>
            </div>
            <div class="flex justify-between text-jade">
                <span>Paid</span>
                <span class="font-mono">{{ $currency }} {{ number_format((float) $invoice->amount_paid) }}</span>
            </div>
            @if($invoice->balance > 0)
                <div class="flex justify-between font-semibold text-amber-700">
                    <span>Balance</span>
                    <span class="font-mono">{{ $currency }} {{ number_format($invoice->balance) }}</span>
                </div>
            @endif
        </div>
    </x-ui.panel>

    @if($invoice->payments->isNotEmpty())
        <x-ui.panel title="Payments received" :padded="false">
            <table class="data-table">
                <thead><tr><th>Receipt</th><th>Date</th><th>Method</th><th class="text-right">Amount</th></tr></thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                        <tr>
                            <td class="font-mono text-xs">{{ $payment->reference }}</td>
                            <td>{{ $payment->paid_at->format('j M Y') }}</td>
                            <td>{{ Str::headline($payment->method) }}</td>
                            <td class="text-right font-mono">{{ $currency }} {{ number_format((float) $payment->amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-ui.panel>
    @endif
</div>
@endsection
