<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardMetrics
{
    public function headline(): array
    {
        $thisMonth = Payment::whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount');
        $lastMonth = Payment::whereMonth('paid_at', now()->subMonth()->month)->whereYear('paid_at', now()->subMonth()->year)->sum('amount');

        return [
            'appointments_today' => Appointment::today()->whereNot('status', Appointment::CANCELLED)->count(),
            'appointments_pending' => Appointment::where('status', Appointment::PENDING)->count(),
            'patients_total' => User::patients()->count(),
            'patients_new_month' => User::patients()->whereMonth('created_at', now()->month)->count(),
            'revenue_month' => (float) $thisMonth,
            'revenue_change' => $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0.0,
            'outstanding' => (float) Invoice::unsettled()->sum(DB::raw('total - amount_paid')),
            'unread_messages' => ContactMessage::unread()->count(),
        ];
    }

    /** Appointments per day for the last 30 days -> line chart. */
    public function appointmentTrend(int $days = 30): array
    {
        $rows = Appointment::query()
            ->selectRaw('DATE(scheduled_on) as day, COUNT(*) as total')
            ->where('scheduled_on', '>=', today()->subDays($days))
            ->groupBy('day')->pluck('total', 'day');

        $labels = [];
        $values = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $labels[] = $date->format('M j');
            $values[] = (int) ($rows[$date->toDateString()] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /** Revenue per month for the last 12 months -> bar chart. */
    public function revenueTrend(int $months = 12): array
    {
        $labels = [];
        $values = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M');
            $values[] = (float) Payment::whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)->sum('amount');
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function statusSplit(): array
    {
        $counts = Appointment::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return collect(config('clinic.appointment_statuses'))
            ->map(fn ($meta, $key) => ['label' => $meta['label'], 'value' => (int) ($counts[$key] ?? 0)])
            ->values()->all();
    }

    public function topServices(int $limit = 5): array
    {
        return Appointment::query()
            ->selectRaw('services.name, COUNT(*) as total')
            ->join('services', 'services.id', '=', 'appointments.service_id')
            ->groupBy('services.name')->orderByDesc('total')->limit($limit)
            ->pluck('total', 'name')->all();
    }

    public function doctorLoad(): array
    {
        return Appointment::query()
            ->selectRaw('users.name, COUNT(*) as total')
            ->join('doctors', 'doctors.id', '=', 'appointments.doctor_id')
            ->join('users', 'users.id', '=', 'doctors.user_id')
            ->whereMonth('appointments.scheduled_on', now()->month)
            ->groupBy('users.name')->orderByDesc('total')
            ->pluck('total', 'name')->all();
    }

    public function forPatient(User $patient): array
    {
        return [
            'upcoming' => $patient->appointments()->upcoming()->count(),
            'completed' => $patient->appointments()->where('status', Appointment::COMPLETED)->count(),
            'records' => $patient->medicalRecords()->count(),
            'due' => (float) $patient->invoices()->unsettled()->sum(DB::raw('total - amount_paid')),
        ];
    }

    public function forDoctor(int $doctorId): array
    {
        return [
            'today' => Appointment::where('doctor_id', $doctorId)->today()->count(),
            'week' => Appointment::where('doctor_id', $doctorId)
                ->between(now()->startOfWeek(), now()->endOfWeek())->count(),
            'patients' => Appointment::where('doctor_id', $doctorId)->distinct('patient_id')->count('patient_id'),
            'pending' => Appointment::where('doctor_id', $doctorId)->where('status', Appointment::PENDING)->count(),
        ];
    }
}
