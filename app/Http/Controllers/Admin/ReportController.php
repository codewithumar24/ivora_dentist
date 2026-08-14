<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Service;
use App\Services\DashboardMetrics;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can.do:billing.view,appointments.view');
    }

    public function index(Request $request, DashboardMetrics $metrics)
    {
        $from = Carbon::parse($request->from ?? now()->startOfMonth());
        $to = Carbon::parse($request->to ?? now()->endOfMonth());

        return view('admin.reports', [
            'from' => $from,
            'to' => $to,
            'summary' => [
                'appointments' => Appointment::between($from, $to)->count(),
                'completed' => Appointment::between($from, $to)->where('status', 'completed')->count(),
                'cancelled' => Appointment::between($from, $to)->where('status', 'cancelled')->count(),
                'no_shows' => Appointment::between($from, $to)->where('status', 'no_show')->count(),
                'collected' => Payment::whereBetween('paid_at', [$from->startOfDay(), $to->endOfDay()])->sum('amount'),
            ],
            'byService' => Appointment::between($from, $to)
                ->selectRaw('services.name, COUNT(*) as bookings, SUM(appointments.fee) as value')
                ->join('services', 'services.id', '=', 'appointments.service_id')
                ->groupBy('services.name')->orderByDesc('bookings')->get(),
            'byDoctor' => Appointment::between($from, $to)
                ->selectRaw('users.name, COUNT(*) as bookings, SUM(appointments.fee) as value')
                ->join('doctors', 'doctors.id', '=', 'appointments.doctor_id')
                ->join('users', 'users.id', '=', 'doctors.user_id')
                ->groupBy('users.name')->orderByDesc('bookings')->get(),
            'byMethod' => Payment::whereBetween('paid_at', [$from->startOfDay(), $to->endOfDay()])
                ->selectRaw('method, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('method')->get(),
            'revenue' => $metrics->revenueTrend(12),
            'trend' => $metrics->appointmentTrend(30),
        ]);
    }

    public function download(Request $request)
    {
        $from = Carbon::parse($request->from ?? now()->startOfMonth());
        $to = Carbon::parse($request->to ?? now()->endOfMonth());

        return Pdf::loadView('pdf.report', [
            'from' => $from,
            'to' => $to,
            'appointments' => Appointment::between($from, $to)->count(),
            'collected' => Payment::whereBetween('paid_at', [$from->startOfDay(), $to->endOfDay()])->sum('amount'),
            'byService' => Appointment::between($from, $to)
                ->selectRaw('services.name, COUNT(*) as bookings, SUM(appointments.fee) as value')
                ->join('services', 'services.id', '=', 'appointments.service_id')
                ->groupBy('services.name')->get(),
        ])->download('ivora-report-'.$from->format('Y-m-d').'-to-'.$to->format('Y-m-d').'.pdf');
    }
}
