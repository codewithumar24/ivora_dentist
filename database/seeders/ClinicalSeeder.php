<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Database\Seeder;

class ClinicalSeeder extends Seeder
{
    public function run(): void
    {
        $findings = [
            ['Pain on chewing, lower right', 'Deep occlusal caries in 46, no periapical radiolucency', 'Irreversible pulpitis 46', 'Access opened, canals cleaned and dressed', 'Avoid chewing on that side until the next visit'],
            ['Routine check-up', 'Generalised mild plaque, no active caries', 'Healthy dentition, gingivitis (mild)', 'Full ultrasonic scale and polish', 'Interdental brushes nightly, review in six months'],
            ['Bleeding gums', 'Bleeding on probing at 4 sites, calculus lower anteriors', 'Localised chronic gingivitis', 'Scaling, oral hygiene instruction', 'Chlorhexidine rinse for 10 days, then stop'],
            ['Chipped upper central', 'Enamel fracture 11, pulp not exposed', 'Uncomplicated crown fracture 11', 'Composite bonding, shade A2', 'No biting into hard fruit for a week'],
            ['Cold sensitivity', 'Cervical abrasion 14, 15 with exposed dentine', 'Dentine hypersensitivity', 'Desensitising varnish applied', 'Switch to a soft brush and sensitive toothpaste'],
        ];

        $medicines = [
            ['Amoxicillin', '500 mg', '1 + 1 + 1', '5 days', 'After food'],
            ['Ibuprofen', '400 mg', '1 + 0 + 1', '3 days', 'Only if sore'],
            ['Chlorhexidine mouthwash', '0.2%', '0 + 1 + 1', '10 days', 'Do not rinse with water afterwards'],
            ['Paracetamol', '500 mg', '1 + 1 + 1', '3 days', 'Maximum 4 doses a day'],
        ];

        $completed = Appointment::where('status', Appointment::COMPLETED)
            ->with('doctor')->inRandomOrder()->take(70)->get();

        foreach ($completed as $appointment) {
            [$complaint, $clinical, $diagnosis, $treatment, $advice] = $findings[array_rand($findings)];

            $record = MedicalRecord::create([
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
                'visited_on' => $appointment->scheduled_on,
                'chief_complaint' => $complaint,
                'clinical_findings' => $clinical,
                'diagnosis' => $diagnosis,
                'treatment_performed' => $treatment,
                'teeth_involved' => collect(['11', '14', '16', '26', '36', '46'])->random(random_int(1, 2))->values()->all(),
                'advice' => $advice,
                'next_visit_on' => random_int(0, 1) ? $appointment->scheduled_on->copy()->addMonths(6) : null,
            ]);

            if (random_int(0, 2) > 0) {
                $prescription = Prescription::create([
                    'medical_record_id' => $record->id,
                    'patient_id' => $record->patient_id,
                    'doctor_id' => $record->doctor_id,
                    'reference' => 'RX-'.now()->format('y').'-'.str_pad((string) $record->id, 5, '0', STR_PAD_LEFT),
                    'issued_on' => $record->visited_on,
                    'notes' => 'Come back sooner if the swelling gets worse.',
                ]);

                foreach (collect($medicines)->random(random_int(1, 3)) as [$name, $dosage, $frequency, $duration, $instruction]) {
                    $prescription->items()->create(compact('dosage', 'frequency', 'duration', 'instruction') + ['medicine' => $name]);
                }
            }
        }
    }
}
