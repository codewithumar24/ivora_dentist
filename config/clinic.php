<?php

/*
|--------------------------------------------------------------------------
| Clinic operating rules
|--------------------------------------------------------------------------
| Values that the front desk changes rarely. Anything a receptionist should
| be able to change day-to-day lives in the `settings` table instead.
*/

return [
    'slot_minutes' => (int) env('CLINIC_SLOT_MINUTES', 30),
    'reminder_hours' => (int) env('CLINIC_REMINDER_HOURS', 24),
    'max_advance_days' => (int) env('CLINIC_MAX_ADVANCE_DAYS', 60),
    'min_notice_minutes' => 60,
    'cancel_window_hours' => 4,

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
    ],

    'appointment_statuses' => [
        'pending'   => ['label' => 'Awaiting confirmation', 'tone' => 'amber'],
        'confirmed' => ['label' => 'Confirmed', 'tone' => 'jade'],
        'completed' => ['label' => 'Completed', 'tone' => 'pine'],
        'cancelled' => ['label' => 'Cancelled', 'tone' => 'rose'],
        'no_show'   => ['label' => 'No show', 'tone' => 'slate'],
    ],
];
