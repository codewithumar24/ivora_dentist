<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        return view('patient.invoices.index', [
            'invoices' => $request->user()->invoices()->with('items', 'payments')
                ->latest('issued_on')->paginate(10),
            'outstanding' => $request->user()->invoices()->unsettled()->get()
                ->sum(fn ($i) => $i->balance),
        ]);
    }

    public function show(Request $request, Invoice $invoice)
    {
        abort_unless($invoice->patient_id === $request->user()->id, 403);

        $invoice->load('items.service', 'payments', 'doctor.user', 'appointment');

        return view('patient.invoices.show', compact('invoice'));
    }

    public function download(Request $request, Invoice $invoice)
    {
        abort_unless($invoice->patient_id === $request->user()->id, 403);

        $invoice->load('items', 'payments', 'patient', 'doctor.user');

        return Pdf::loadView('pdf.invoice', compact('invoice'))->download("{$invoice->number}.pdf");
    }
}
