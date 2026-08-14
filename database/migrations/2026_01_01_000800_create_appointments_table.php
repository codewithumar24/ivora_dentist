<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();      // IVD-2026-00042
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->date('scheduled_on');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->enum('source', ['online', 'phone', 'walk_in', 'admin'])->default('online');
            $table->enum('visit_type', ['first_visit', 'follow_up', 'emergency'])->default('first_visit');
            $table->text('reason')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('staff_notes')->nullable();
            $table->decimal('fee', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cancel_reason')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scheduled_on', 'status']);
            $table->index(['doctor_id', 'scheduled_on']);
            $table->unique(['doctor_id', 'scheduled_on', 'starts_at'], 'unique_doctor_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
