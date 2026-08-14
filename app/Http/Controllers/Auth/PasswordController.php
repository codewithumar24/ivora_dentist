<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function __construct(protected PasswordResetService $resets) {}

    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $this->resets->sendLink($request->email);

        // Same response either way so the form cannot be used to discover accounts.
        return back()->with('status', 'If that address has an Ivora account, a reset link is on its way.');
    }

    public function resetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $done = $this->resets->reset($data['email'], $data['token'], $data['password']);

        if (! $done) {
            return back()->withErrors(['email' => 'That reset link has expired. Request a new one.']);
        }

        return redirect()->route('login')->with('status', 'Password updated. Sign in with the new one.');
    }
}
