<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['clinic_name', 'Ivora Dental Studio', 'text', 'general', 'Clinic name'],
            ['tagline', 'Quiet, careful dentistry', 'text', 'general', 'Tagline'],
            ['currency_symbol', 'Rs', 'text', 'general', 'Currency symbol'],
            ['timezone', 'Asia/Karachi', 'text', 'general', 'Timezone'],

            // Contact
            ['phone', '+92 300 1234567', 'text', 'contact', 'Reception phone'],
            ['emergency_phone', '+92 300 7654321', 'text', 'contact', 'Out-of-hours line'],
            ['email', 'care@ivoradental.com', 'text', 'contact', 'Email'],
            ['address', '2nd Floor, Meridian Court, Zamzama Boulevard, Karachi', 'textarea', 'contact', 'Address'],
            ['map_embed', 'https://maps.google.com/maps?q=karachi&t=&z=13&ie=UTF8&iwloc=&output=embed', 'text', 'contact', 'Map embed URL'],
            ['whatsapp', '+92 300 1234567', 'text', 'contact', 'WhatsApp'],

            // Hours
            ['hours_weekday', '9:00 AM – 7:00 PM', 'text', 'hours', 'Monday to Friday'],
            ['hours_saturday', '10:00 AM – 4:00 PM', 'text', 'hours', 'Saturday'],
            ['hours_sunday', 'Emergencies only', 'text', 'hours', 'Sunday'],

            // Social
            ['facebook', 'https://facebook.com', 'text', 'social', 'Facebook'],
            ['instagram', 'https://instagram.com', 'text', 'social', 'Instagram'],
            ['linkedin', 'https://linkedin.com', 'text', 'social', 'LinkedIn'],
            ['youtube', 'https://youtube.com', 'text', 'social', 'YouTube'],

            // Booking behaviour
            ['booking_note', 'New patients: please arrive 10 minutes early with any previous x-rays.', 'textarea', 'booking', 'Note shown on the booking form'],
            ['auto_confirm', '0', 'boolean', 'booking', 'Confirm online bookings automatically'],

            // SEO
            ['meta_title', 'Ivora Dental Studio — dentistry without the dread, Karachi', 'text', 'seo', 'Default page title'],
            ['meta_description', 'A calm dental studio in Karachi. Same-week appointments, transparent pricing, and clinicians who explain everything before they start.', 'textarea', 'seo', 'Meta description'],
        ];

        foreach ($settings as [$key, $value, $type, $group, $label]) {
            Setting::updateOrCreate(['key' => $key], compact('value', 'type', 'group', 'label'));
        }
    }
}
