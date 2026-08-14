<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('visited_on');
            $table->string('chief_complaint')->nullable();
            $table->text('clinical_findings')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_performed')->nullable();
            $table->json('teeth_involved')->nullable(); // FDI notation e.g. ["16","26"]
            $table->text('advice')->nullable();
            $table->date('next_visit_on')->nullable();
            $table->json('attachments')->nullable(); // x-rays, scans
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'visited_on']);
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 20)->unique();
            $table->date('issued_on');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->string('medicine', 150);
            $table->string('dosage', 80)->nullable();     // 500mg
            $table->string('frequency', 80)->nullable();  // 1 + 0 + 1
            $table->string('duration', 80)->nullable();   // 5 days
            $table->string('instruction', 180)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medical_records');
    }
};
