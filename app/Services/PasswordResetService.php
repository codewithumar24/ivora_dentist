<?php

namespace App\Services;

use App\Mail\PasswordResetLink;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/** Hand-rolled reset flow so the token lifetime and mail template stay ours. */
class PasswordResetService
{
    public function sendLink(string $email): bool
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return false;
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        Mail::to($email)->queue(new PasswordResetLink($user, $token));

        return true;
    }

    public function tokenIsValid(string $email, string $token): bool
    {
        $row = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $row || ! Hash::check($token, $row->token)) {
            return false;
        }

        return now()->diffInMinutes($row->created_at) < config('auth.passwords.users.expire', 60);
    }

    public function reset(string $email, string $token, string $password): bool
    {
        if (! $this->tokenIsValid($email, $token)) {
            return false;
        }

        User::where('email', $email)->update(['password' => Hash::make($password)]);
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
    }
}
