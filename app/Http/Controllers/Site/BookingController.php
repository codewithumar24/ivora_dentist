<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Service;
use App\Services\AppointmentService;
use App\Services\SlotService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    public function __construct(
        protected SlotService $slots,
        protected AppointmentService $appointments
    ) {}

    public function create(Request $request)
    {
        return view('site.book', [
            'doctors' => Doctor::with('user', 'speciality')->bookable()->orderBy('sort_order')->get(),
            'services' => Service::active()->orderBy('sort_order')->get(),
            'selectedDoctor' => $request->doctor,
            'selectedService' => $request->service,
        ]);
    }

    /** Called by the date strip in the booking form. */
    public function slots(Request $request)
    {
        $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'date' => ['required', 'date'],
        ]);

        $doctor = Doctor::findOrFail($request->doctor_id);
        $slots = $this->slots->availableFor($doctor, Carbon::parse($request->date));

        return response()->json([
            'date' => Carbon::parse($request->date)->format('l j F Y'),
            'slots' => $slots->groupBy('period'),
            'count' => $slots->count(),
        ]);
    }

    public function days(Request $request)
    {
        $doctor = Doctor::findOrFail($request->doctor_id);

        return response()->json($this->slots->upcomingOpenDays($doctor, 14));
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

        if (! auth()->check()) {
            session(['booking.intent' => $data]);

            return redirect()->route('register')
                ->with('status', 'Create an account to hold this slot — it takes about 30 seconds.');
        }

        $appointment = $this->appointments->book($request->user(), $data);

        return redirect()->route('patient.appointments.show', $appointment)
            ->with('status', "Request sent. Your reference is {$appointment->reference}.");
    }
}
