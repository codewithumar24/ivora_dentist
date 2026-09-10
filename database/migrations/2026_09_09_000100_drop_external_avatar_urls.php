<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The team seeder wrote randomuser.me URLs into users.avatar. On any network
 * where that host is slow or blocked — which includes most clinic and office
 * connections — every doctor card renders as a blank grey box.
 *
 * Clearing the column lets User::getAvatarUrlAttribute() fall back to the local
 * initials SVG, which always renders. Real uploads are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('avatar', 'like', 'https://randomuser.me/%')
            ->orWhere('avatar', 'like', 'https://ui-avatars.com/%')
            ->update(['avatar' => null]);
    }

    public function down(): void
    {
        // Nothing to restore: the URLs were placeholder data, not clinic content.
    }
};
