<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PatientsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(protected ?string $search = null) {}

    public function query()
    {
        return User::query()->patients()->with('patientProfile')->search($this->search)->latest();
    }

    public function headings(): array
    {
        return ['MRN', 'Name', 'Email', 'Phone', 'Gender', 'Age', 'City', 'Blood group', 'Allergies', 'Registered', 'Status'];
    }

    public function map($user): array
    {
        return [
            $user->patientProfile?->mrn,
            $user->name,
            $user->email,
            $user->phone,
            $user->gender ? ucfirst($user->gender) : '—',
            $user->age ?? '—',
            $user->city,
            $user->patientProfile?->blood_group ?? '—',
            $user->patientProfile?->allergies ?? 'None recorded',
            $user->created_at->format('Y-m-d'),
            ucfirst($user->status),
        ];
    }
}
