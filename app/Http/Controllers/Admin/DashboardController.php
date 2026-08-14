<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\Invoice;
use App\Models\User;
use App\Services\DashboardMetrics;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardMetrics $metrics)
    {
        $user = $request->user();

        // A dentist who is not the owner sees their own chair, not the whole clinic.
        $ownDoctorId = $user->doctor?->id;
        $scoped = $ownDoctorId && ! $user->hasPermission('appointments.view');

        return view('admin.dashboard', [
            'headline' => $metrics->headline(),
            'personal' => $ownDoctorId ? $metrics->forDoctor($ownDoctorId) : null,
            'trend' => $metrics->appointmentTrend(30),
            'revenue' => $metrics->revenueTrend(12),
            'statusSplit' => $metrics->statusSplit(),
            'topServices' => $metrics->topServices(),
            'doctorLoad' => $metrics->doctorLoad(),
            'today' => Appointment::with('patient', 'doctor.user', 'service')
                ->today()->when($scoped, fn ($q) => $q->where('doctor_id', $ownDoctorId))
                ->orderBy('starts_at')->get(),
            'pending' => Appointment::with('patient', 'doctor.user')
                ->where('status', Appointment::PENDING)->orderBy('scheduled_on')->take(6)->get(),
            'newPatients' => User::patients()->latest()->take(5)->get(),
            'unpaid' => Invoice::unsettled()->with('patient')->latest('issued_on')->take(5)->get(),
            'messages' => ContactMessage::unread()->latest()->take(4)->get(),
            'activity' => ActivityLog::with('user')->latest()->take(8)->get(),
        ]);
    }
}
