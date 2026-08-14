<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AppointmentsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        return Appointment::query()
            ->with(['patient', 'doctor.user', 'service'])
            ->when($this->filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($this->filters['doctor_id'] ?? null, fn ($q, $d) => $q->where('doctor_id', $d))
            ->when($this->filters['from'] ?? null, fn ($q, $f) => $q->whereDate('scheduled_on', '>=', $f))
            ->when($this->filters['to'] ?? null, fn ($q, $t) => $q->whereDate('scheduled_on', '<=', $t))
            ->latest('scheduled_on');
    }

    public function headings(): array
    {
        return ['Reference', 'Date', 'Time', 'Patient', 'Phone', 'Practitioner', 'Treatment', 'Status', 'Fee', 'Payment'];
    }

    public function map($appointment): array
    {
        return [
            $appointment->reference,
            $appointment->scheduled_on->format('Y-m-d'),
            $appointment->time_label,
            $appointment->patient?->name,
            $appointment->patient?->phone,
            $appointment->doctor?->full_name,
            $appointment->service?->name ?? '—',
            $appointment->status_label,
            $appointment->fee,
            ucfirst($appointment->payment_status),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
