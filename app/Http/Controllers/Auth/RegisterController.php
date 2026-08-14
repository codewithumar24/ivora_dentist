<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeToIvora;
use App\Models\ActivityLog;
use App\Models\PatientProfile;
use App\Models\Role;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register', [
            'pendingBooking' => session('booking.intent'),
        ]);
    }

    public function store(Request $request, VerificationService $verification, AppointmentService $appointments)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'city' => ['nullable', 'string', 'max:120'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'terms' => ['accepted'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'role_id' => Role::where('slug', Role::PATIENT)->value('id'),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'city' => $data['city'] ?? null,
                'password' => $data['password'],
                'status' => 'active',
            ]);

            PatientProfile::create([
                'user_id' => $user->id,
                'mrn' => PatientProfile::nextMrn(),
            ]);

            return $user;
        });

        $verification->issue($user);
        Mail::to($user->email)->queue(new WelcomeToIvora($user));
        ActivityLog::record('created', "New patient registered: {$user->name}", $user);

        Auth::login($user);
        $request->session()->regenerate();

        // Someone who started booking before signing up gets their slot finished off.
        if ($intent = session()->pull('booking.intent')) {
            try {
                $appointment = $appointments->book($user, $intent);

                return redirect()->route('patient.appointments.show', $appointment)
                    ->with('status', "Welcome to Ivora. Your slot is held under {$appointment->reference}.");
            } catch (\Throwable) {
                return redirect()->route('book')
                    ->with('status', 'Account created, but that slot just went. Pick another time.');
            }
        }

        return redirect()->route('patient.dashboard')
            ->with('status', 'Welcome to Ivora. Check your inbox to confirm your email address.');
    }
}
