<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Speciality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecialityServiceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['General dentistry', 'tooth', 'Check-ups, cleaning and everyday repair work — the appointments most people need most.', [
                ['Comprehensive check-up', 3000, 4500, 30, 'A full look at teeth, gums, bite and soft tissue, with digital x-rays if needed.', true],
                ['Scale and polish', 4500, 7000, 45, 'Ultrasonic cleaning to lift plaque and staining, finished with a polish.', true],
                ['Tooth-coloured filling', 6000, 12000, 45, 'Composite fillings shaded to the tooth, bonded in a single visit.', false],
                ['Fluoride treatment', 2500, 3500, 20, 'A protective varnish for patients prone to decay.', false],
            ]],
            ['Cosmetic dentistry', 'sparkle', 'Work aimed at how a smile looks, planned with a preview before anything is touched.', [
                ['Professional whitening', 18000, 28000, 60, 'In-chair whitening with custom trays for topping up at home.', true],
                ['Porcelain veneers', 35000, 60000, 90, 'Hand-layered veneers, shaped from a wax-up you approve first.', true],
                ['Composite bonding', 9000, 18000, 60, 'Chips, gaps and worn edges rebuilt in one appointment.', false],
                ['Smile design consultation', 5000, null, 45, 'A photographic and digital plan for the smile you want.', false],
            ]],
            ['Orthodontics', 'braces', 'Straightening for teenagers and adults, from clear aligners to fixed appliances.', [
                ['Clear aligners', 180000, 320000, 45, 'A full aligner course with scans, refinements and retainers included.', true],
                ['Ceramic braces', 150000, 240000, 60, 'Tooth-shaded brackets for a less visible fixed treatment.', false],
                ['Retainer fitting', 12000, 20000, 30, 'Fixed or removable retainers to hold the result.', false],
            ]],
            ['Oral surgery', 'scalpel', 'Extractions and minor surgical work under local anaesthetic, with sedation available.', [
                ['Simple extraction', 8000, 12000, 30, 'Removal of a tooth that cannot be saved, with aftercare instructions.', false],
                ['Wisdom tooth removal', 20000, 45000, 60, 'Surgical removal, planned from a panoramic x-ray.', true],
                ['Dental implant', 120000, 200000, 90, 'Titanium implant, healing period, then a custom crown.', true],
            ]],
            ['Endodontics', 'root', 'Root canal treatment done under magnification to save teeth that would otherwise come out.', [
                ['Root canal — single root', 25000, 35000, 75, 'Cleaning, shaping and sealing a single-canal tooth.', false],
                ['Root canal — molar', 40000, 55000, 90, 'Multi-canal treatment with rotary instruments and a rubber dam.', true],
                ['Re-treatment', 45000, 65000, 90, 'Revising a previous root canal that has not settled.', false],
            ]],
            ['Paediatric dentistry', 'child', 'A gentler room, shorter appointments, and no surprises for children.', [
                ['Child check-up', 2000, 3000, 25, 'A short, unhurried first visit with a ride in the chair.', true],
                ['Fissure sealants', 3500, 6000, 30, 'Sealing the grooves of new molars before decay can start.', false],
                ['Habit counselling', 3000, null, 30, 'Practical help with thumb-sucking, grinding and diet.', false],
            ]],
        ];

        foreach ($data as $index => [$name, $icon, $description, $services]) {
            $speciality = Speciality::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'icon' => $icon, 'description' => $description, 'sort_order' => $index]
            );

            foreach ($services as $order => [$serviceName, $from, $to, $duration, $summary, $featured]) {
                Service::updateOrCreate(
                    ['slug' => Str::slug($serviceName)],
                    [
                        'speciality_id' => $speciality->id,
                        'name' => $serviceName,
                        'summary' => $summary,
                        'description' => $summary.' '.$description,
                        'price_from' => $from,
                        'price_to' => $to,
                        'duration_minutes' => $duration,
                        'is_featured' => $featured,
                        'sort_order' => $order,
                        'icon' => $icon,
                        'includes' => ['Consultation', 'Treatment plan in writing', 'Follow-up call'],
                    ]
                );
            }
        }
    }
}
