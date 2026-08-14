<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('speciality_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->string('icon', 60)->nullable();
            $table->string('image')->nullable();
            $table->string('summary', 255)->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price_from', 10, 2)->default(0);
            $table->decimal('price_to', 10, 2)->nullable();
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->json('includes')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
