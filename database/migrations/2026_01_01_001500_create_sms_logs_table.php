<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('to', 32);
            $table->text('body');
            $table->string('purpose', 60)->default('general')->index(); // reminder|confirmation|otp
            $table->string('driver', 20)->default('log');
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->string('provider_id', 120)->nullable();
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
