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
            $table->uuid('uuid')->unique();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('service_type', ['konsultasi', 'ortosis', 'protesis', 'terapi', 'alat']);
            $table->decimal('price', 15, 2);
            $table->integer('duration_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['code', 'service_type']);
            $table->index(['is_active', 'service_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
