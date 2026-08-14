<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingSeeder::class,
            SpecialityServiceSeeder::class,
            TeamSeeder::class,
            PatientSeeder::class,
            AppointmentSeeder::class,
            ClinicalSeeder::class,
            BillingSeeder::class,
            ContentSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('Ivora is seeded. Sign in with:');
        $this->command->table(['Role', 'Email', 'Password'], [
            ['Owner (principal dentist)', 'owner@ivoradental.com', 'password'],
            ['Dentist', 'dentist@ivoradental.com', 'password'],
            ['Receptionist', 'reception@ivoradental.com', 'password'],
            ['Accountant', 'accounts@ivoradental.com', 'password'],
            ['Content editor', 'editor@ivoradental.com', 'password'],
            ['Patient', 'patient@example.com', 'password'],
        ]);
    }
}
