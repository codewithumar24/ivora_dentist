@extends('layouts.console')
@section('heading', 'Payments ledger')
@section('subheading', $payments->total().' receipts')

@section('actions')
    @permission('billing.export')
        <a href="{{ route('admin.billing.payments.export', $filters) }}" class="btn btn-ghost btn-sm">Export</a>
    @endpermission
@endsection

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="space-y-5">
    <form method="GET" class="card-flat filter-bar p-4 flex flex-wrap gap-3 items-end">
        <div><label class="field-label">From</label><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="field !py-2 !text-sm"></div>
        <div><label class="field-label">To</label><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="field !py-2 !text-sm"></div>
        <button class="btn btn-primary btn-sm">Filter</button>
    </form>

    <x-ui.panel :padded="false">
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>Receipt</th><th>Paid at</th><th>Patient</th><th>Invoice</th><th>Method</th><th class="text-right">Amount</th></tr></thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="font-mono text-xs">{{ $payment->reference }}</td>
                            <td class="whitespace-nowrap">{{ $payment->paid_at->format('j M Y') }}</td>
                            <td class="truncate max-w-[160px]">{{ $payment->patient->name }}</td>
                            <td class="font-mono text-xs">{{ $payment->invoice?->number }}</td>
                            <td><span class="badge badge-mint">{{ Str::headline($payment->method) }}</span></td>
                            <td class="text-right font-mono font-medium">{{ $currency }} {{ number_format((float) $payment->amount) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-ui.empty title="No payments in this range" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $payments->links() }}
</div>
@endsection
