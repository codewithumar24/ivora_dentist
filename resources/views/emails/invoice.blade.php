@extends('emails.layout')
@section('preheader', 'Invoice '.$invoice->number)

@section('body')
    @php $currency = \App\Models\Setting::get('currency_symbol', 'Rs'); @endphp

    <p style="margin:0 0 18px;font-size:22px;font-weight:600;letter-spacing:-.02em;">Invoice {{ $invoice->number }}</p>

    <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#33514C;">
        Here is the itemised invoice for your treatment on {{ $invoice->issued_on->format('j F Y') }}.
        A PDF copy is always available in your patient portal.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:20px;">
        <thead>
            <tr>
                <th align="left" style="font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#8AA39E;padding:0 0 8px;">Item</th>
                <th align="right" style="font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#8AA39E;padding:0 0 8px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td style="font-size:14px;padding:9px 0;border-top:1px solid rgba(16,64,59,.08);">{{ $item->description }}</td>
                    <td align="right" style="font-size:14px;padding:9px 0;border-top:1px solid rgba(16,64,59,.08);">{{ $currency }} {{ number_format((float) $item->line_total) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="font-size:15px;font-weight:600;padding:12px 0 0;border-top:2px solid #10403B;">Total</td>
                <td align="right" style="font-size:15px;font-weight:600;padding:12px 0 0;border-top:2px solid #10403B;">{{ $currency }} {{ number_format((float) $invoice->total) }}</td>
            </tr>
            @if($invoice->balance > 0)
                <tr>
                    <td style="font-size:14px;padding:6px 0 0;color:#8A5A12;">Balance due</td>
                    <td align="right" style="font-size:14px;padding:6px 0 0;color:#8A5A12;font-weight:600;">{{ $currency }} {{ number_format($invoice->balance) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <x-mail-button :url="route('patient.invoices.show', $invoice)" label="View invoice" />
@endsection
