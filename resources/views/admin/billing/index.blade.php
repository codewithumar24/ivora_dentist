@extends('layouts.console')
@section('heading', 'Invoices')
@section('subheading', $invoices->total().' raised')

@section('actions')
    @permission('billing.export')
        <a href="{{ route('admin.billing.export', $filters) }}" class="btn btn-ghost btn-sm">Export</a>
    @endpermission
    @permission('billing.manage')
        <a href="{{ route('admin.billing.create') }}" class="btn btn-primary btn-sm">New invoice</a>
    @endpermission
@endsection

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="space-y-5">
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-ui.stat label="Total billed" :value="$currency.' '.number_format($totals['billed'])" tone="pine" icon="M5 3h10v14l-2.5-1.5L10 17l-2.5-1.5L5 17z" />
        <x-ui.stat label="Collected" :value="$currency.' '.number_format($totals['collected'])" tone="jade" icon="M3 6h14v9H3zM3 9h14" />
        <x-ui.stat label="Outstanding" :value="$currency.' '.number_format($totals['outstanding'])" tone="amber" icon="M10 5v5l3 2" />
        <x-ui.stat label="This month" :value="$currency.' '.number_format($totals['this_month'])" tone="iris" icon="M4 16V8M8 16V4M12 16v-6M16 16v-9" />
    </div>

    <form method="GET" class="card-flat filter-bar p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="field-label">Search</label>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Invoice number or patient" class="field !py-2 !text-sm">
        </div>
        <div>
            <label class="field-label">Status</label>
            <select name="status" class="field !py-2 !text-sm">
                <option value="">Any</option>
                @foreach(['unpaid', 'partial', 'paid', 'void', 'draft'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary btn-sm">Filter</button>
        <a href="{{ route('admin.billing.payments') }}" class="btn btn-ghost btn-sm">Payments ledger</a>
    </form>

    <x-ui.panel :padded="false">
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>Invoice</th><th>Patient</th><th>Issued</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="font-mono text-xs">{{ $invoice->number }}</td>
                            <td class="truncate max-w-[160px]">{{ $invoice->patient->name }}</td>
                            <td class="whitespace-nowrap">{{ $invoice->issued_on->format('j M Y') }}</td>
                            <td class="font-mono">{{ number_format((float) $invoice->total) }}</td>
                            <td class="font-mono text-jade">{{ number_format((float) $invoice->amount_paid) }}</td>
                            <td class="font-mono font-medium">{{ number_format($invoice->balance) }}</td>
                            <td><span class="badge badge-{{ match($invoice->status) { 'paid' => 'jade', 'partial' => 'amber', 'void' => 'slate', default => 'rose' } }}">{{ ucfirst($invoice->status) }}</span></td>
                            <td class="text-right"><a href="{{ route('admin.billing.show', $invoice) }}" class="btn btn-ghost btn-sm">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-ui.empty title="No invoices" message="Raise one from a completed appointment." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $invoices->links() }}
</div>
@endsection
