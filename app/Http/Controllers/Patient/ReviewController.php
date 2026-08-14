<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->patient_id === $request->user()->id, 403);
        abort_unless($appointment->status === Appointment::COMPLETED, 403, 'You can review a visit once it is completed.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:1500'],
        ]);

        Review::updateOrCreate(
            ['appointment_id' => $appointment->id, 'patient_id' => $request->user()->id],
            $data + ['doctor_id' => $appointment->doctor_id]
        );

        return back()->with('status', 'Thanks — your feedback goes to the practice manager first, then the site.');
    }
}
