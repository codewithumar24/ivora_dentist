@extends('layouts.portal')
@section('heading', 'Invoices')
@section('subheading', 'Every charge, itemised')

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="space-y-5 max-w-4xl">
    @if($outstanding > 0)
        <div class="card-flat p-6 border-l-4 border-l-amber-400 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="eyebrow text-pine/45 mb-1.5">Outstanding balance</p>
                <p class="font-display text-3xl font-bold">{{ $currency }} {{ number_format($outstanding) }}</p>
            </div>
            <p class="text-sm text-pine/60 max-w-xs">Payable at reception by cash, card or bank transfer.</p>
        </div>
    @endif

    <x-ui.panel :padded="false">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr><th>Invoice</th><th>Issued</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="font-mono text-xs">{{ $invoice->number }}</td>
                            <td>{{ $invoice->issued_on->format('j M Y') }}</td>
                            <td>{{ $currency }} {{ number_format((float) $invoice->total) }}</td>
                            <td>{{ $currency }} {{ number_format((float) $invoice->amount_paid) }}</td>
                            <td class="font-medium">{{ $currency }} {{ number_format($invoice->balance) }}</td>
                            <td>
                                <span class="badge badge-{{ match($invoice->status) { 'paid' => 'jade', 'partial' => 'amber', 'void' => 'slate', default => 'rose' } }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('patient.invoices.show', $invoice) }}" class="btn btn-ghost btn-sm">View</a>
                                <a href="{{ route('patient.invoices.pdf', $invoice) }}" class="btn btn-ghost btn-sm">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-ui.empty title="No invoices" message="Charges appear here after treatment." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $invoices->links() }}
</div>
@endsection
