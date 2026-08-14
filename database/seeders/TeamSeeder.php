<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Role;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $dentists = [
            [
                'name' => 'Ayesha Rahim', 'email' => 'owner@ivoradental.com', 'phone' => '+923001234501',
                'role' => Role::OWNER, 'speciality' => 'cosmetic-dentistry', 'title' => 'Dr.',
                'designation' => 'Principal dentist & founder', 'qualifications' => 'BDS, MSc Restorative Dentistry (KCL)',
                'experience' => 18, 'fee' => 5000, 'featured' => true,
                'photo' => 'https://randomuser.me/api/portraits/women/44.jpg',
                'bio' => 'Founded Ivora in 2012 after a decade in restorative practice in London. Known for taking the time to show patients what she is looking at before she touches anything.',
            ],
            [
                'name' => 'Bilal Ahsan', 'email' => 'dentist@ivoradental.com', 'phone' => '+923001234502',
                'role' => Role::DENTIST, 'speciality' => 'oral-surgery', 'title' => 'Dr.',
                'designation' => 'Oral surgeon', 'qualifications' => 'BDS, FCPS Oral & Maxillofacial Surgery',
                'experience' => 12, 'fee' => 4500, 'featured' => true,
                'photo' => 'https://randomuser.me/api/portraits/men/32.jpg',
                'bio' => 'Handles the surgical list — wisdom teeth, implants and the cases other clinics refer on. Unflappable, and very fast with a suture.',
            ],
            [
                'name' => 'Sana Qureshi', 'email' => 'sana@ivoradental.com', 'phone' => '+923001234503',
                'role' => Role::DENTIST, 'speciality' => 'orthodontics', 'title' => 'Dr.',
                'designation' => 'Orthodontist', 'qualifications' => 'BDS, MOrth RCS (Edin)',
                'experience' => 9, 'fee' => 4000, 'featured' => true,
                'photo' => 'https://randomuser.me/api/portraits/women/68.jpg',
                'bio' => 'Aligners and fixed appliances for teenagers and adults. Plans every case digitally so patients can see the finish line on day one.',
            ],
            [
                'name' => 'Hamza Iqbal', 'email' => 'hamza@ivoradental.com', 'phone' => '+923001234504',
                'role' => Role::DENTIST, 'speciality' => 'endodontics', 'title' => 'Dr.',
                'designation' => 'Endodontist', 'qualifications' => 'BDS, MSc Endodontics',
                'experience' => 11, 'fee' => 4500, 'featured' => false,
                'photo' => 'https://randomuser.me/api/portraits/men/75.jpg',
                'bio' => 'Root canals under a microscope. Takes on re-treatments that have failed elsewhere and is unreasonably cheerful about it.',
            ],
            [
                'name' => 'Zainab Farid', 'email' => 'zainab@ivoradental.com', 'phone' => '+923001234505',
                'role' => Role::DENTIST, 'speciality' => 'paediatric-dentistry', 'title' => 'Dr.',
                'designation' => 'Paediatric dentist', 'qualifications' => 'BDS, MSc Paediatric Dentistry',
                'experience' => 7, 'fee' => 3500, 'featured' => true,
                'photo' => 'https://randomuser.me/api/portraits/women/12.jpg',
                'bio' => 'Runs the children\'s room. First visits are ten minutes long and involve no instruments at all — that comes later.',
            ],
            [
                'name' => 'Omar Sheikh', 'email' => 'omar@ivoradental.com', 'phone' => '+923001234506',
                'role' => Role::DENTIST, 'speciality' => 'general-dentistry', 'title' => 'Dr.',
                'designation' => 'General dentist', 'qualifications' => 'BDS, PG Dip Restorative',
                'experience' => 6, 'fee' => 3000, 'featured' => false,
                'photo' => 'https://randomuser.me/api/portraits/men/22.jpg',
                'bio' => 'Check-ups, hygiene and everyday restorative work. Most patients at Ivora meet Omar first.',
            ],
        ];

        foreach ($dentists as $index => $row) {
            $user = User::updateOrCreate(['email' => $row['email']], [
                'role_id' => Role::where('slug', $row['role'])->value('id'),
                'name' => $row['name'],
                'phone' => $row['phone'],
                'password' => 'password',
                'avatar' => $row['photo'],
                'status' => 'active',
                'email_verified_at' => now(),
                'city' => 'Karachi',
            ]);

            $doctor = Doctor::updateOrCreate(['user_id' => $user->id], [
                'speciality_id' => Speciality::where('slug', $row['speciality'])->value('id'),
                'title' => $row['title'],
                'slug' => Str::slug($row['title'].' '.$row['name']),
                'designation' => $row['designation'],
                'qualifications' => $row['qualifications'],
                'license_no' => 'PMDC-'.random_int(10000, 99999),
                'experience_years' => $row['experience'],
                'consultation_fee' => $row['fee'],
                'short_bio' => Str::limit($row['bio'], 140),
                'biography' => $row['bio'],
                'languages' => ['English', 'Urdu'],
                'socials' => ['linkedin' => 'https://linkedin.com', 'instagram' => 'https://instagram.com'],
                'rating' => round(random_int(45, 50) / 10, 2),
                'reviews_count' => random_int(18, 96),
                'is_featured' => $row['featured'],
                'sort_order' => $index,
            ]);

            $doctor->services()->sync(
                Service::where('speciality_id', $doctor->speciality_id)->pluck('id')
            );

            // Sunday closed, Saturday half day.
            foreach ([1, 2, 3, 4, 5, 6] as $day) {
                DoctorSchedule::updateOrCreate(
                    ['doctor_id' => $doctor->id, 'day_of_week' => $day],
                    [
                        'starts_at' => $day === 6 ? '10:00' : '09:00',
                        'ends_at' => $day === 6 ? '16:00' : '19:00',
                        'break_starts_at' => '13:00',
                        'break_ends_at' => '14:00',
                        'slot_minutes' => 30,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Non-clinical staff.
        $staff = [
            ['Farah Nadeem', 'reception@ivoradental.com', '+923001234507', Role::RECEPTIONIST, 'https://randomuser.me/api/portraits/women/29.jpg'],
            ['Imran Baig', 'accounts@ivoradental.com', '+923001234508', Role::ACCOUNTANT, 'https://randomuser.me/api/portraits/men/54.jpg'],
            ['Mehwish Ali', 'editor@ivoradental.com', '+923001234509', Role::EDITOR, 'https://randomuser.me/api/portraits/women/90.jpg'],
            ['Danish Rauf', 'assistant@ivoradental.com', '+923001234510', Role::ASSISTANT, 'https://randomuser.me/api/portraits/men/11.jpg'],
        ];

        foreach ($staff as [$name, $email, $phone, $role, $photo]) {
            User::updateOrCreate(['email' => $email], [
                'role_id' => Role::where('slug', $role)->value('id'),
                'name' => $name,
                'phone' => $phone,
                'password' => 'password',
                'avatar' => $photo,
                'status' => 'active',
                'email_verified_at' => now(),
                'city' => 'Karachi',
            ]);
        }
    }
}
