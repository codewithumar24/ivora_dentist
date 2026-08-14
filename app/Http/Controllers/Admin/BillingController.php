<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InvoicesExport;
use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use App\Mail\InvoiceIssued;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can.do:billing.view')->only(['index', 'show', 'payments']);
        $this->middleware('can.do:billing.manage')->only(['create', 'store', 'update', 'recordPayment', 'destroy', 'send']);
        $this->middleware('can.do:billing.export')->only(['export', 'exportPayments']);
    }

    public function index(Request $request)
    {
        $invoices = Invoice::with('patient', 'doctor.user')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->q, fn ($q, $term) => $q->where('number', 'like', "%{$term}%")
                ->orWhereRelation('patient', 'name', 'like', "%{$term}%"))
            ->latest('issued_on')->paginate(15)->withQueryString();

        return view('admin.billing.index', [
            'invoices' => $invoices,
            'totals' => [
                'billed' => Invoice::whereNot('status', 'void')->sum('total'),
                'collected' => Payment::sum('amount'),
                'outstanding' => Invoice::unsettled()->sum(DB::raw('total - amount_paid')),
                'this_month' => Payment::whereMonth('paid_at', now()->month)->sum('amount'),
            ],
            'filters' => $request->only('status', 'q'),
        ]);
    }

    public function create()
    {
        return view('admin.billing.create', [
            'patients' => User::patients()->orderBy('name')->get(['id', 'name']),
            'services' => Service::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:users,id'],
            'issued_on' => ['required', 'date'],
            'due_on' => ['nullable', 'date', 'after_or_equal:issued_on'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:200'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $invoice = DB::transaction(function () use ($data) {
            $invoice = Invoice::create(collect($data)->except('items')->all() + [
                'number' => Invoice::nextNumber(),
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $invoice->items()->create($item);
            }

            $invoice->recalculate();

            return $invoice;
        });

        return redirect()->route('admin.billing.show', $invoice)->with('status', "Invoice {$invoice->number} raised.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items.service', 'payments', 'patient', 'doctor.user', 'appointment');

        return view('admin.billing.show', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->validate([
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'due_on' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:draft,unpaid,partial,paid,void'],
        ]));

        $invoice->recalculate();

        return back()->with('status', 'Invoice updated.');
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.max($invoice->balance, 0.01)],
            'method' => ['required', 'in:cash,card,bank_transfer,insurance,online'],
            'paid_at' => ['required', 'date'],
            'transaction_id' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $invoice->payments()->create($data + [
            'patient_id' => $invoice->patient_id,
            'reference' => Payment::nextReference(),
            'received_by' => auth()->id(),
        ]);

        $invoice->recalculate();

        if ($invoice->appointment) {
            $invoice->appointment->update([
                'payment_status' => $invoice->balance <= 0 ? 'paid' : 'partial',
            ]);
        }

        return back()->with('status', 'Payment recorded and the receipt is ready.');
    }

    public function send(Invoice $invoice)
    {
        Mail::to($invoice->patient->email)->queue(new InvoiceIssued($invoice));

        return back()->with('status', 'Invoice emailed to the patient.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('items', 'payments', 'patient', 'doctor.user');

        return Pdf::loadView('pdf.invoice', compact('invoice'))->download("{$invoice->number}.pdf");
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('admin.billing.index')->with('status', 'Invoice voided.');
    }

    public function payments(Request $request)
    {
        return view('admin.billing.payments', [
            'payments' => Payment::with('patient', 'invoice')
                ->when($request->from, fn ($q, $f) => $q->whereDate('paid_at', '>=', $f))
                ->when($request->to, fn ($q, $t) => $q->whereDate('paid_at', '<=', $t))
                ->latest('paid_at')->paginate(20)->withQueryString(),
            'filters' => $request->only('from', 'to'),
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new InvoicesExport($request->only('status', 'from', 'to')),
            'ivora-invoices-'.now()->format('Y-m-d').'.xlsx');
    }

    public function exportPayments(Request $request)
    {
        return Excel::download(new PaymentsExport($request->only('from', 'to')),
            'ivora-payments-'.now()->format('Y-m-d').'.xlsx');
    }
}
