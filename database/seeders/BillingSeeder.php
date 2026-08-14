<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = Appointment::with('service')
            ->where('status', Appointment::COMPLETED)->get();

        $counter = 0;

        foreach ($appointments as $appointment) {
            $counter++;

            $invoice = Invoice::create([
                'number' => 'INV-'.$appointment->scheduled_on->format('Y').'-'.str_pad((string) $counter, 5, '0', STR_PAD_LEFT),
                'patient_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'issued_on' => $appointment->scheduled_on,
                'due_on' => $appointment->scheduled_on->copy()->addDays(7),
                'discount' => random_int(0, 6) === 0 ? 1000 : 0,
                'tax' => 0,
            ]);

            $invoice->items()->create([
                'service_id' => $appointment->service_id,
                'description' => $appointment->service?->name ?? 'Dental consultation',
                'quantity' => 1,
                'unit_price' => $appointment->fee,
            ]);

            if (random_int(0, 4) === 0) {
                $invoice->items()->create([
                    'description' => 'Digital x-ray',
                    'quantity' => 1,
                    'unit_price' => 1500,
                ]);
            }

            $invoice->recalculate();

            // Most get paid, some sit part-paid, a few are still open.
            $roll = random_int(1, 10);

            if ($roll <= 7) {
                $this->pay($invoice, $invoice->total, $appointment->scheduled_on);
            } elseif ($roll <= 9) {
                $this->pay($invoice, round($invoice->total / 2, 2), $appointment->scheduled_on);
            }

            $invoice->recalculate();
        }
    }

    protected function pay(Invoice $invoice, float $amount, $date): void
    {
        Payment::create([
            'invoice_id' => $invoice->id,
            'patient_id' => $invoice->patient_id,
            'reference' => 'PAY-'.$date->format('ymd').'-'.strtoupper(substr(md5($invoice->id.$amount), 0, 5)),
            'amount' => $amount,
            'method' => ['cash', 'card', 'bank_transfer', 'insurance', 'online'][array_rand(['cash', 'card', 'bank_transfer', 'insurance', 'online'])],
            'paid_at' => $date,
        ]);
    }
}
