<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index(Request $request)
    {
        return view('patient.records.index', [
            'records' => $request->user()->medicalRecords()
                ->with('doctor.user', 'prescription.items', 'appointment')
                ->latest('visited_on')->paginate(8),
        ]);
    }

    public function show(Request $request, MedicalRecord $record)
    {
        abort_unless($record->patient_id === $request->user()->id, 403);

        $record->load('doctor.user', 'prescription.items', 'appointment');

        return view('patient.records.show', compact('record'));
    }

    public function prescriptions(Request $request)
    {
        return view('patient.records.prescriptions', [
            'prescriptions' => $request->user()->prescriptions()
                ->with('items', 'doctor.user')->latest('issued_on')->paginate(10),
        ]);
    }

    public function downloadPrescription(Request $request, Prescription $prescription)
    {
        abort_unless($prescription->patient_id === $request->user()->id, 403);

        $prescription->load('items', 'doctor.user', 'patient');

        return Pdf::loadView('pdf.prescription', compact('prescription'))
            ->download("{$prescription->reference}.pdf");
    }
}
