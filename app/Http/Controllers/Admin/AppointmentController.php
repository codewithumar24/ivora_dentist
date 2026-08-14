<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AppointmentsExport;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\SlotService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentService $appointments,
        protected SlotService $slots
    ) {
        $this->middleware('can.do:appointments.view')->only(['index', 'calendar', 'show']);
        $this->middleware('can.do:appointments.create')->only(['create', 'store']);
        $this->middleware('can.do:appointments.update')->only(['edit', 'update', 'status', 'reschedule']);
        $this->middleware('can.do:appointments.delete')->only('destroy');
        $this->middleware('can.do:appointments.export')->only(['export', 'dayList']);
    }

    public function index(Request $request)
    {
        $appointments = Appointment::with('patient', 'doctor.user', 'service')
            ->status($request->status)
            ->when($request->doctor_id, fn ($q, $d) => $q->where('doctor_id', $d))
            ->when($request->from, fn ($q, $f) => $q->whereDate('scheduled_on', '>=', $f))
            ->when($request->to, fn ($q, $t) => $q->whereDate('scheduled_on', '<=', $t))
            ->when($request->q, fn ($q, $term) => $q->where('reference', 'like', "%{$term}%")
                ->orWhereRelation('patient', 'name', 'like', "%{$term}%"))
            ->orderByDesc('scheduled_on')->orderBy('starts_at')
            ->paginate(15)->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'doctors' => Doctor::with('user')->get(),
            'filters' => $request->only('status', 'doctor_id', 'from', 'to', 'q'),
        ]);
    }

    public function calendar(Request $request)
    {
        $date = Carbon::parse($request->date ?? today());

        return view('admin.appointments.calendar', [
            'date' => $date,
            'doctors' => Doctor::with('user')->active()->get(),
            'appointments' => Appointment::with('patient', 'doctor.user', 'service')
                ->whereBetween('scheduled_on', [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()])
                ->orderBy('starts_at')->get()->groupBy(fn ($a) => $a->scheduled_on->toDateString()),
            'weekStart' => $date->copy()->startOfWeek(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.appointments.create', [
            'patients' => User::patients()->orderBy('name')->get(['id', 'name', 'phone', 'email']),
            'doctors' => Doctor::with('user')->active()->get(),
            'services' => Service::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:users,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'scheduled_on' => ['required', 'date'],
            'starts_at' => ['required', 'date_format:H:i'],
            'visit_type' => ['required', 'in:first_visit,follow_up,emergency'],
            'source' => ['required', 'in:phone,walk_in,admin,online'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $patient = User::findOrFail($data['patient_id']);
        $appointment = $this->appointments->book($patient, $data, $data['source']);

        return redirect()->route('admin.appointments.show', $appointment)
            ->with('status', "Booked — {$appointment->reference}.");
    }

    public function show(Appointment $appointment)
    {
        $appointment->load('patient.patientProfile', 'doctor.user', 'service', 'medicalRecord.prescription.items', 'invoice.items');

        return view('admin.appointments.show', compact('appointment'));
    }

    public function status(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
            'cancel_reason' => ['nullable', 'string', 'max:200'],
        ]);

        $this->appointments->changeStatus($appointment, $data['status'], $data['cancel_reason'] ?? null);

        return back()->with('status', "Marked as {$appointment->status_label}.");
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'scheduled_on' => ['required', 'date'],
            'starts_at' => ['required', 'date_format:H:i'],
        ]);

        $this->appointments->reschedule($appointment, $data['scheduled_on'], $data['starts_at']);

        return back()->with('status', 'Moved and the patient has been told.');
    }

    public function update(Request $request, Appointment $appointment)
    {
        $appointment->update($request->validate([
            'staff_notes' => ['nullable', 'string', 'max:2000'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['nullable', 'in:unpaid,partial,paid,refunded'],
        ]));

        return back()->with('status', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('status', 'Appointment removed.');
    }

    public function export(Request $request)
    {
        $filters = $request->only('status', 'doctor_id', 'from', 'to');
        $name = 'ivora-appointments-'.now()->format('Y-m-d');

        return match ($request->get('format', 'xlsx')) {
            'csv' => Excel::download(new AppointmentsExport($filters), "{$name}.csv", \Maatwebsite\Excel\Excel::CSV),
            'pdf' => Pdf::loadView('pdf.appointments', [
                'appointments' => (new AppointmentsExport($filters))->query()->get(),
                'filters' => $filters,
            ])->setPaper('a4', 'landscape')->download("{$name}.pdf"),
            default => Excel::download(new AppointmentsExport($filters), "{$name}.xlsx"),
        };
    }

    /** Printable chair list handed to the nurse each morning. */
    public function dayList(Request $request)
    {
        $date = Carbon::parse($request->date ?? today());

        return Pdf::loadView('pdf.day-sheet', [
            'date' => $date,
            'appointments' => Appointment::with('patient', 'doctor.user', 'service')
                ->whereDate('scheduled_on', $date)->orderBy('starts_at')->get(),
        ])->download('ivora-day-sheet-'.$date->format('Y-m-d').'.pdf');
    }
}
