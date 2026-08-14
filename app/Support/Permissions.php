<?php

namespace App\Support;

/**
 * The full permission catalogue. Seeded into the permissions table and used by
 * the `can.do` middleware, the @can blade checks and the role editor screen.
 */
class Permissions
{
    public const CATALOGUE = [
        'Appointments' => [
            'appointments.view' => 'See the appointment calendar and list',
            'appointments.create' => 'Book on behalf of a patient',
            'appointments.update' => 'Reschedule, confirm and edit appointments',
            'appointments.delete' => 'Remove appointments',
            'appointments.export' => 'Export appointment data',
        ],
        'Patients' => [
            'patients.view' => 'Open patient records',
            'patients.create' => 'Register a new patient',
            'patients.update' => 'Edit patient details',
            'patients.delete' => 'Archive a patient',
            'patients.export' => 'Export the patient list',
        ],
        'Clinical' => [
            'records.view' => 'Read clinical notes',
            'records.create' => 'Write clinical notes and prescriptions',
            'records.update' => 'Amend clinical notes',
        ],
        'Doctors' => [
            'doctors.view' => 'See the practitioner directory',
            'doctors.manage' => 'Add, edit and deactivate practitioners',
            'schedules.manage' => 'Set working hours and time off',
        ],
        'Billing' => [
            'billing.view' => 'See invoices and payments',
            'billing.manage' => 'Raise invoices and record payments',
            'billing.export' => 'Export financial reports',
        ],
        'Content' => [
            'services.manage' => 'Manage treatments and pricing',
            'blog.manage' => 'Write and publish articles',
            'gallery.manage' => 'Manage the gallery and testimonials',
            'faqs.manage' => 'Manage FAQs',
        ],
        'Front desk' => [
            'messages.view' => 'Read enquiries from the website',
            'messages.reply' => 'Reply to enquiries',
            'subscribers.manage' => 'Manage the mailing list',
        ],
        'Administration' => [
            'users.view' => 'See staff accounts',
            'users.manage' => 'Create and edit staff accounts',
            'roles.manage' => 'Create roles and change permissions',
            'settings.manage' => 'Change clinic settings',
            'activity.view' => 'Read the audit trail',
        ],
    ];

    public static function flat(): array
    {
        $out = [];

        foreach (self::CATALOGUE as $group => $items) {
            foreach ($items as $slug => $description) {
                $out[] = ['slug' => $slug, 'group' => $group, 'description' => $description];
            }
        }

        return $out;
    }
}
