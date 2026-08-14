<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        return Payment::query()->with(['patient', 'invoice'])
            ->when($this->filters['from'] ?? null, fn ($q, $f) => $q->whereDate('paid_at', '>=', $f))
            ->when($this->filters['to'] ?? null, fn ($q, $t) => $q->whereDate('paid_at', '<=', $t))
            ->latest('paid_at');
    }

    public function headings(): array
    {
        return ['Receipt', 'Paid at', 'Patient', 'Invoice', 'Method', 'Amount', 'Transaction'];
    }

    public function map($payment): array
    {
        return [
            $payment->reference,
            $payment->paid_at?->format('Y-m-d H:i'),
            $payment->patient?->name,
            $payment->invoice?->number,
            ucfirst(str_replace('_', ' ', $payment->method)),
            $payment->amount,
            $payment->transaction_id ?? '—',
        ];
    }
}
