<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;
use App\Services\AppointmentService;
use App\Services\SlotService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentService $appointments,
        protected SlotService $slots
    ) {}

    public function index(Request $request)
    {
        $query = $request->user()->appointments()->with('doctor.user', 'service');

        return view('patient.appointments.index', [
            'appointments' => (clone $query)->status($request->status)
                ->latest('scheduled_on')->latest('starts_at')->paginate(10)->withQueryString(),
            'counts' => [
                'upcoming' => (clone $query)->upcoming()->count(),
                'completed' => (clone $query)->where('status', Appointment::COMPLETED)->count(),
                'cancelled' => (clone $query)->where('status', Appointment::CANCELLED)->count(),
            ],
            'status' => $request->status,
        ]);
    }

    public function create(Request $request)
    {
        return view('patient.appointments.create', [
            'doctors' => Doctor::with('user', 'speciality')->bookable()->get(),
            'services' => Service::active()->orderBy('name')->get(),
            'selectedDoctor' => $request->doctor,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'scheduled_on' => ['required', 'date', 'after_or_equal:today'],
            'starts_at' => ['required', 'date_format:H:i'],
            'visit_type' => ['required', 'in:first_visit,follow_up,emergency'],
            'reason' => ['nullable', 'string', 'max:500'],
            'symptoms' => ['nullable', 'string', 'max:1000'],
        ]);

        $appointment = $this->appointments->book($request->user(), $data);

        return redirect()->route('patient.appointments.show', $appointment)
            ->with('status', "Request sent. Reference {$appointment->reference}.");
    }

    public function show(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->patient_id === $request->user()->id, 403);

        $appointment->load('doctor.user', 'doctor.speciality', 'service', 'medicalRecord.prescription.items', 'invoice');

        return view('patient.appointments.show', compact('appointment'));
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->patient_id === $request->user()->id, 403);
        abort_unless($appointment->isCancellableByPatient(), 403, 'This visit is too close to move online. Call the front desk.');

        $data = $request->validate([
            'scheduled_on' => ['required', 'date', 'after_or_equal:today'],
            'starts_at' => ['required', 'date_format:H:i'],
        ]);

        $this->appointments->reschedule($appointment, $data['scheduled_on'], $data['starts_at']);

        return back()->with('status', 'Moved. You will get a fresh confirmation shortly.');
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->patient_id === $request->user()->id, 403);

        if (! $appointment->isCancellableByPatient()) {
            return back()->withErrors([
                'cancel' => 'Cancellations close '.config('clinic.cancel_window_hours').' hours before the visit. Call us on the number in the footer.',
            ]);
        }

        $request->validate(['cancel_reason' => ['nullable', 'string', 'max:200']]);

        $this->appointments->changeStatus($appointment, Appointment::CANCELLED, $request->cancel_reason);

        return redirect()->route('patient.appointments.index')->with('status', 'Appointment cancelled.');
    }
}
