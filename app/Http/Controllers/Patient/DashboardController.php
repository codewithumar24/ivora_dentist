<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Services\DashboardMetrics;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardMetrics $metrics)
    {
        $patient = $request->user();

        return view('patient.dashboard', [
            'stats' => $metrics->forPatient($patient),
            'next' => $patient->appointments()->upcoming()->with('doctor.user', 'service')->orderBy('scheduled_on')->orderBy('starts_at')->first(),
            'upcoming' => $patient->appointments()->upcoming()->with('doctor.user', 'service')->orderBy('scheduled_on')->take(4)->get(),
            'records' => $patient->medicalRecords()->with('doctor.user')->latest('visited_on')->take(3)->get(),
            'invoices' => $patient->invoices()->unsettled()->latest('issued_on')->take(3)->get(),
            'notifications' => $patient->unreadNotifications()->take(5)->get(),
        ]);
    }
}
