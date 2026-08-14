<?php

namespace Database\Seeders;

use App\Models\PatientProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $roleId = Role::where('slug', Role::PATIENT)->value('id');

        $demo = User::updateOrCreate(['email' => 'patient@example.com'], [
            'role_id' => $roleId,
            'name' => 'Hina Saeed',
            'phone' => '+923331112233',
            'password' => 'password',
            'gender' => 'female',
            'date_of_birth' => '1993-04-18',
            'city' => 'Karachi',
            'address' => 'Flat 4B, Seaview Apartments, Clifton, Karachi',
            'status' => 'active',
            'email_verified_at' => now(),
            'avatar' => 'https://randomuser.me/api/portraits/women/65.jpg',
        ]);

        PatientProfile::updateOrCreate(['user_id' => $demo->id], [
            'mrn' => 'IVD-P00001',
            'blood_group' => 'B+',
            'allergies' => 'Penicillin',
            'medical_conditions' => 'None',
            'emergency_contact_name' => 'Saeed Ahmed',
            'emergency_contact_phone' => '+923331112244',
            'insurance_provider' => 'Jubilee Health',
            'insurance_number' => 'JH-882913',
        ]);

        $names = [
            ['Ali Raza', 'male'], ['Fatima Noor', 'female'], ['Usman Tariq', 'male'],
            ['Maryam Javed', 'female'], ['Shahzaib Khan', 'male'], ['Areeba Siddiqui', 'female'],
            ['Kamran Aslam', 'male'], ['Noor Fatima', 'female'], ['Zeeshan Haider', 'male'],
            ['Rabia Anwar', 'female'], ['Faisal Mehmood', 'male'], ['Sadia Bashir', 'female'],
            ['Junaid Akram', 'male'], ['Hira Malik', 'female'], ['Adnan Yousuf', 'male'],
            ['Komal Rehman', 'female'], ['Waleed Anjum', 'male'], ['Sidra Kamal', 'female'],
            ['Talha Munir', 'male'], ['Nimra Shah', 'female'], ['Salman Dar', 'male'],
            ['Amna Riaz', 'female'], ['Haris Latif', 'male'], ['Iqra Sultan', 'female'],
        ];

        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+'];
        $cities = ['Karachi', 'Lahore', 'Hyderabad', 'Islamabad', 'Sukkur'];

        foreach ($names as $index => [$name, $gender]) {
            $user = User::updateOrCreate(
                ['email' => Str::slug($name, '.').'@example.com'],
                [
                    'role_id' => $roleId,
                    'name' => $name,
                    'phone' => '+9233'.str_pad((string) (10000000 + $index * 7), 8, '0', STR_PAD_LEFT),
                    'password' => 'password',
                    'gender' => $gender,
                    'date_of_birth' => now()->subYears(random_int(9, 64))->subDays(random_int(0, 364))->toDateString(),
                    'city' => $cities[array_rand($cities)],
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'created_at' => now()->subDays(random_int(1, 400)),
                    'avatar' => sprintf(
                        'https://randomuser.me/api/portraits/%s/%d.jpg',
                        $gender === 'female' ? 'women' : 'men',
                        random_int(1, 98)
                    ),
                ]
            );

            PatientProfile::updateOrCreate(['user_id' => $user->id], [
                'mrn' => 'IVD-P'.str_pad((string) ($index + 2), 5, '0', STR_PAD_LEFT),
                'blood_group' => $bloodGroups[array_rand($bloodGroups)],
                'allergies' => random_int(0, 4) === 0 ? 'Latex' : null,
                'is_smoker' => random_int(0, 5) === 0,
            ]);
        }
    }
}
