<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        return Invoice::query()->with(['patient', 'doctor.user'])
            ->when($this->filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($this->filters['from'] ?? null, fn ($q, $f) => $q->whereDate('issued_on', '>=', $f))
            ->when($this->filters['to'] ?? null, fn ($q, $t) => $q->whereDate('issued_on', '<=', $t))
            ->latest('issued_on');
    }

    public function headings(): array
    {
        return ['Invoice', 'Issued', 'Due', 'Patient', 'Practitioner', 'Subtotal', 'Discount', 'Tax', 'Total', 'Paid', 'Balance', 'Status'];
    }

    public function map($invoice): array
    {
        return [
            $invoice->number,
            $invoice->issued_on?->format('Y-m-d'),
            $invoice->due_on?->format('Y-m-d'),
            $invoice->patient?->name,
            $invoice->doctor?->full_name ?? '—',
            $invoice->subtotal,
            $invoice->discount,
            $invoice->tax,
            $invoice->total,
            $invoice->amount_paid,
            $invoice->balance,
            ucfirst($invoice->status),
        ];
    }
}
