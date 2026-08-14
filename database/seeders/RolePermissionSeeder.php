<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Permissions::flat() as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => Str::headline(str_replace('.', ' ', $permission['slug'])),
                    'group' => $permission['group'],
                    'description' => $permission['description'],
                ]
            );
        }

        $roles = [
            [
                'slug' => Role::OWNER,
                'name' => 'Clinic owner',
                'level' => Role::LEVEL_OWNER,
                'is_locked' => true,
                'description' => 'The principal dentist. Holds every permission and decides what everyone else can reach.',
                'permissions' => '*',
            ],
            [
                'slug' => Role::DENTIST,
                'name' => 'Dentist',
                'level' => Role::LEVEL_CLINICAL,
                'description' => 'Treats patients, writes clinical notes and prescriptions, sees their own diary.',
                'permissions' => [
                    'appointments.view', 'appointments.create', 'appointments.update',
                    'patients.view', 'patients.update',
                    'records.view', 'records.create', 'records.update',
                    'doctors.view', 'schedules.manage', 'billing.view',
                ],
            ],
            [
                'slug' => Role::RECEPTIONIST,
                'name' => 'Receptionist',
                'level' => Role::LEVEL_STAFF,
                'description' => 'Runs the front desk: books, reschedules, registers patients, answers enquiries.',
                'permissions' => [
                    'appointments.view', 'appointments.create', 'appointments.update', 'appointments.export',
                    'patients.view', 'patients.create', 'patients.update',
                    'doctors.view', 'billing.view', 'messages.view', 'messages.reply', 'subscribers.manage',
                ],
            ],
            [
                'slug' => Role::ASSISTANT,
                'name' => 'Dental assistant',
                'level' => Role::LEVEL_STAFF,
                'description' => 'Chairside support. Reads the diary and clinical notes, writes neither.',
                'permissions' => ['appointments.view', 'patients.view', 'records.view', 'doctors.view'],
            ],
            [
                'slug' => Role::ACCOUNTANT,
                'name' => 'Accountant',
                'level' => Role::LEVEL_STAFF,
                'description' => 'Invoices, payments and financial exports. No clinical access.',
                'permissions' => [
                    'billing.view', 'billing.manage', 'billing.export',
                    'patients.view', 'appointments.view', 'appointments.export',
                ],
            ],
            [
                'slug' => Role::EDITOR,
                'name' => 'Content editor',
                'level' => Role::LEVEL_STAFF,
                'description' => 'Marketing seat: treatments, articles, gallery and FAQs only.',
                'permissions' => ['services.manage', 'blog.manage', 'gallery.manage', 'faqs.manage', 'subscribers.manage'],
            ],
            [
                'slug' => Role::PATIENT,
                'name' => 'Patient',
                'level' => Role::LEVEL_PATIENT,
                'is_locked' => true,
                'description' => 'Portal access only: own appointments, records, prescriptions and invoices.',
                'permissions' => [],
            ],
        ];

        foreach ($roles as $definition) {
            $role = Role::updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'name' => $definition['name'],
                    'level' => $definition['level'],
                    'is_locked' => $definition['is_locked'] ?? false,
                    'description' => $definition['description'],
                ]
            );

            $permissions = $definition['permissions'] === '*'
                ? Permission::pluck('id')
                : Permission::whereIn('slug', $definition['permissions'])->pluck('id');

            $role->permissions()->sync($permissions);
        }
    }
}
