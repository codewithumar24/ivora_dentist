<?php

namespace App\Services;

use App\Mail\VerifyEmailAddress;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VerificationService
{
    public function issue(User $user): string
    {
        $token = Str::random(64);

        DB::table('email_verification_tokens')->where('user_id', $user->id)->delete();
        DB::table('email_verification_tokens')->insert([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'expires_at' => now()->addHours(48),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($user->email)->queue(new VerifyEmailAddress($user, $token));

        return $token;
    }

    public function confirm(string $token): ?User
    {
        $row = DB::table('email_verification_tokens')
            ->where('token', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->first();

        if (! $row) {
            return null;
        }

        $user = User::find($row->user_id);
        $user?->forceFill(['email_verified_at' => now()])->save();

        DB::table('email_verification_tokens')->where('id', $row->id)->delete();

        return $user;
    }
}
