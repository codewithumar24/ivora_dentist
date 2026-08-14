<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeExpiredTokens extends Command
{
    protected $signature = 'ivora:purge-tokens';

    protected $description = 'Delete expired password reset and email verification tokens';

    public function handle(): int
    {
        $resets = DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subHours(24))->delete();

        $verifications = DB::table('email_verification_tokens')
            ->where('expires_at', '<', now())->delete();

        $this->info("Removed {$resets} reset token(s) and {$verifications} verification token(s).");

        return self::SUCCESS;
    }
}
