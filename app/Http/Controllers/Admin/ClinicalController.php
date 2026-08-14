<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicalController extends Controller
{
    public function __construct()
    {
        $this->middleware('can.do:records.view')->only(['index', 'show']);
        $this->middleware('can.do:records.create')->only(['create', 'store']);
        $this->middleware('can.do:records.update')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        return view('admin.records.index', [
            'records' => MedicalRecord::with('patient', 'doctor.user')
                ->when($request->q, fn ($q, $term) => $q->whereRelation('patient', 'name', 'like', "%{$term}%"))
                ->latest('visited_on')->paginate(15)->withQueryString(),
        ]);
    }

    public function create(Request $request)
    {
        $appointment = $request->appointment
            ? Appointment::with('patient.patientProfile', 'doctor.user', 'service')->findOrFail($request->appointment)
            : null;

        return view('admin.records.create', [
            'appointment' => $appointment,
            'patients' => User::patients()->orderBy('name')->get(['id', 'name']),
            'doctors' => \App\Models\Doctor::with('user')->active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'patient_id' => ['required', 'exists:users,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'visited_on' => ['required', 'date'],
            'chief_complaint' => ['nullable', 'string', 'max:255'],
            'clinical_findings' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'treatment_performed' => ['nullable', 'string'],
            'teeth_involved' => ['nullable', 'array'],
            'advice' => ['nullable', 'string'],
            'next_visit_on' => ['nullable', 'date', 'after:today'],
            'medicines' => ['nullable', 'array'],
            'medicines.*.medicine' => ['nullable', 'string', 'max:150'],
        ]);

        $record = DB::transaction(function () use ($request, $data) {
            $record = MedicalRecord::create(collect($data)->except('medicines')->all());

            $medicines = collect($data['medicines'] ?? [])->filter(fn ($m) => ! empty($m['medicine']));

            if ($medicines->isNotEmpty()) {
                $prescription = Prescription::create([
                    'medical_record_id' => $record->id,
                    'patient_id' => $record->patient_id,
                    'doctor_id' => $record->doctor_id,
                    'reference' => Prescription::nextReference(),
                    'issued_on' => $record->visited_on,
                    'notes' => $request->input('prescription_notes'),
                ]);

                foreach ($medicines as $medicine) {
                    $prescription->items()->create($medicine);
                }
            }

            return $record;
        });

        return redirect()->route('admin.records.show', $record)->with('status', 'Clinical note saved.');
    }

    public function show(MedicalRecord $record)
    {
        $record->load('patient.patientProfile', 'doctor.user', 'prescription.items', 'appointment');

        return view('admin.records.show', compact('record'));
    }

    public function edit(MedicalRecord $record)
    {
        return view('admin.records.edit', [
            'record' => $record->load('prescription.items'),
            'doctors' => \App\Models\Doctor::with('user')->active()->get(),
        ]);
    }

    public function update(Request $request, MedicalRecord $record)
    {
        $record->update($request->validate([
            'visited_on' => ['required', 'date'],
            'chief_complaint' => ['nullable', 'string', 'max:255'],
            'clinical_findings' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'treatment_performed' => ['nullable', 'string'],
            'advice' => ['nullable', 'string'],
            'next_visit_on' => ['nullable', 'date'],
        ]));

        return back()->with('status', 'Clinical note updated.');
    }

    public function printPrescription(Prescription $prescription)
    {
        $prescription->load('items', 'patient', 'doctor.user');

        return Pdf::loadView('pdf.prescription', compact('prescription'))
            ->download("{$prescription->reference}.pdf");
    }
}
