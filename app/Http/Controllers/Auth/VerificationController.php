<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\VerificationService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(protected VerificationService $verification) {}

    public function notice()
    {
        return view('auth.verify-email');
    }

    public function verify(string $token)
    {
        $user = $this->verification->confirm($token);

        if (! $user) {
            return redirect()->route('verification.notice')
                ->withErrors(['token' => 'That confirmation link has expired. Send a new one below.']);
        }

        return redirect()->route('patient.dashboard')->with('status', 'Email confirmed. Thanks.');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('status', 'That address is already confirmed.');
        }

        $this->verification->issue($request->user());

        return back()->with('status', 'New confirmation link sent.');
    }
}
