<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = Doctor::with('services')->get();
        $patients = User::patients()->get();
        $slots = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'];
        $reasons = [
            'Ache on the lower right when chewing',
            'Six-month check-up',
            'Bleeding gums for the last two weeks',
            'Chipped a front tooth',
            'Want to discuss aligners',
            'Sensitivity to cold',
            'Crown feels loose',
            'Whitening consultation',
        ];

        $created = 0;
        $seen = [];

        // 120 days of history plus 30 days ahead.
        for ($dayOffset = -120; $dayOffset <= 30; $dayOffset++) {
            $date = today()->addDays($dayOffset);

            if ($date->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }

            $perDay = $dayOffset <= 0 ? random_int(3, 9) : random_int(1, 5);

            for ($i = 0; $i < $perDay; $i++) {
                $doctor = $doctors->random();
                $service = $doctor->services->isNotEmpty() ? $doctor->services->random() : Service::inRandomOrder()->first();
                $time = $slots[array_rand($slots)];
                $key = $doctor->id.'|'.$date->toDateString().'|'.$time;

                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;

                $status = match (true) {
                    $dayOffset > 0 => random_int(0, 3) === 0 ? Appointment::PENDING : Appointment::CONFIRMED,
                    $dayOffset === 0 => Appointment::CONFIRMED,
                    random_int(0, 11) === 0 => Appointment::CANCELLED,
                    random_int(0, 15) === 0 => Appointment::NO_SHOW,
                    default => Appointment::COMPLETED,
                };

                $created++;

                Appointment::create([
                    'reference' => sprintf('IVD-%d-%05d', $date->year, $created),
                    'patient_id' => $patients->random()->id,
                    'doctor_id' => $doctor->id,
                    'service_id' => $service?->id,
                    'scheduled_on' => $date->toDateString(),
                    'starts_at' => $time,
                    'ends_at' => Carbon::parse($time)->addMinutes($service?->duration_minutes ?? 30)->format('H:i'),
                    'status' => $status,
                    'source' => ['online', 'phone', 'walk_in', 'admin'][array_rand(['online', 'phone', 'walk_in', 'admin'])],
                    'visit_type' => random_int(0, 2) === 0 ? 'follow_up' : 'first_visit',
                    'reason' => $reasons[array_rand($reasons)],
                    'fee' => $service?->price_from ?? $doctor->consultation_fee,
                    'payment_status' => $status === Appointment::COMPLETED ? 'paid' : 'unpaid',
                    'confirmed_at' => in_array($status, [Appointment::CONFIRMED, Appointment::COMPLETED]) ? $date->copy()->subDay() : null,
                    'completed_at' => $status === Appointment::COMPLETED ? $date : null,
                    'cancelled_at' => $status === Appointment::CANCELLED ? $date->copy()->subDay() : null,
                    'created_at' => $date->copy()->subDays(random_int(1, 14)),
                ]);
            }
        }

        $this->command->info("Seeded {$created} appointments across five months.");
    }
}
