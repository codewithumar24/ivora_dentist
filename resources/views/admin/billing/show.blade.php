@extends('layouts.console')
@section('heading', 'Invoice '.$invoice->number)
@section('subheading', $invoice->patient->name.' · issued '.$invoice->issued_on->format('j F Y'))

@section('actions')
    <a href="{{ route('admin.billing.pdf', $invoice) }}" class="btn btn-ghost btn-sm">PDF</a>
    @permission('billing.manage')
        <form action="{{ route('admin.billing.send', $invoice) }}" method="POST">
            @csrf
            <button class="btn btn-primary btn-sm">Email to patient</button>
        </form>
    @endpermission
@endsection

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="grid lg:grid-cols-[1fr_330px] gap-6 items-start max-w-6xl">
    <div class="space-y-5">
        <x-ui.panel :padded="false">
            <div class="p-5 border-b border-pine/8 flex flex-wrap justify-between gap-5 text-sm">
                <div>
                    <p class="eyebrow text-pine/45 mb-1.5">Billed to</p>
                    <p class="font-medium">{{ $invoice->patient->name }}</p>
                    <p class="text-pine/60">{{ $invoice->patient->email }}</p>
                    <p class="text-pine/60 font-mono text-xs">{{ $invoice->patient->patientProfile?->mrn }}</p>
                </div>
                <div class="text-right">
                    <span class="badge badge-{{ match($invoice->status) { 'paid' => 'jade', 'partial' => 'amber', 'void' => 'slate', default => 'rose' } }}">{{ ucfirst($invoice->status) }}</span>
                    <p class="text-pine/60 mt-2">Due {{ $invoice->due_on?->format('j M Y') ?? 'on receipt' }}</p>
                    @if($invoice->doctor)<p class="text-pine/60">{{ $invoice->doctor->full_name }}</p>@endif
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

            <div class="p-5 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-pine/60">Subtotal</span><span class="font-mono">{{ $currency }} {{ number_format((float) $invoice->subtotal) }}</span></div>
                @if((float) $invoice->discount)<div class="flex justify-between"><span class="text-pine/60">Discount</span><span class="font-mono">− {{ $currency }} {{ number_format((float) $invoice->discount) }}</span></div>@endif
                @if((float) $invoice->tax)<div class="flex justify-between"><span class="text-pine/60">Tax</span><span class="font-mono">{{ $currency }} {{ number_format((float) $invoice->tax) }}</span></div>@endif
                <div class="flex justify-between pt-3 mt-2 border-t border-pine/10 font-semibold text-base"><span>Total</span><span class="font-mono">{{ $currency }} {{ number_format((float) $invoice->total) }}</span></div>
                <div class="flex justify-between text-jade"><span>Paid</span><span class="font-mono">{{ $currency }} {{ number_format((float) $invoice->amount_paid) }}</span></div>
                @if($invoice->balance > 0)
                    <div class="flex justify-between font-semibold text-amber-700"><span>Balance</span><span class="font-mono">{{ $currency }} {{ number_format($invoice->balance) }}</span></div>
                @endif
            </div>
        </x-ui.panel>

        @if($invoice->payments->isNotEmpty())
            <x-ui.panel title="Payments" :padded="false">
                <table class="data-table">
                    <thead><tr><th>Receipt</th><th>Date</th><th>Method</th><th>Transaction</th><th class="text-right">Amount</th></tr></thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                            <tr>
                                <td class="font-mono text-xs">{{ $payment->reference }}</td>
                                <td>{{ $payment->paid_at->format('j M Y') }}</td>
                                <td>{{ Str::headline($payment->method) }}</td>
                                <td class="font-mono text-xs">{{ $payment->transaction_id ?? '—' }}</td>
                                <td class="text-right font-mono">{{ $currency }} {{ number_format((float) $payment->amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-ui.panel>
        @endif
    </div>

    <aside class="space-y-5 lg:sticky lg:top-24">
        @permission('billing.manage')
            @if($invoice->balance > 0)
                <x-ui.panel title="Record a payment">
                    <form action="{{ route('admin.billing.payment', $invoice) }}" method="POST" class="space-y-4">
                        @csrf
                        <x-ui.field label="Amount" name="amount" type="number" step="0.01" :value="$invoice->balance" required />
                        <div>
                            <label class="field-label" for="method">Method</label>
                            <select name="method" id="method" class="field">
                                @foreach(['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank transfer', 'insurance' => 'Insurance', 'online' => 'Online'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-ui.field label="Received on" name="paid_at" type="date" :value="now()->toDateString()" required />
                        <x-ui.field label="Transaction ref" name="transaction_id" />
                        <button class="btn btn-primary w-full">Record payment</button>
                    </form>
                </x-ui.panel>
            @endif

            <x-ui.panel title="Adjust">
                <form action="{{ route('admin.billing.update', $invoice) }}" method="POST" class="space-y-4">
                    @csrf @method('PATCH')
                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.field label="Discount" name="discount" type="number" step="0.01" :value="$invoice->discount" />
                        <x-ui.field label="Tax" name="tax" type="number" step="0.01" :value="$invoice->tax" />
                    </div>
                    <x-ui.field label="Due date" name="due_on" type="date" :value="$invoice->due_on?->toDateString()" />
                    <div>
                        <label class="field-label" for="status">Status</label>
                        <select name="status" id="status" class="field">
                            @foreach(['draft', 'unpaid', 'partial', 'paid', 'void'] as $status)
                                <option value="{{ $status }}" @selected($invoice->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-ghost w-full">Save</button>
                </form>

                <form action="{{ route('admin.billing.destroy', $invoice) }}" method="POST" class="mt-4 pt-4 border-t border-pine/8"
                      data-confirm="Void this invoice?">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm w-full border border-rose-200 text-rose-700 hover:bg-rose-50">Void invoice</button>
                </form>
            </x-ui.panel>
        @endpermission
    </aside>
</div>
@endsection
