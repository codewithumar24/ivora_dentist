<?php

namespace App\Support;

use App\Models\User;

class Redirects
{
    /** Where an account lands after signing in. */
    public static function homeFor(User $user): string
    {
        if ($user->canAccessConsole()) {
            return route('admin.dashboard');
        }

        return route('patient.dashboard');
    }
}
