<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('medical_record_number', 20)->unique();
            $table->string('nik', 16)->nullable();
            $table->string('name');
            $table->date('date_of_birth');
            $table->enum('gender', ['L', 'P']);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->enum('insurance_type', ['bpjs', 'mandiri', 'asuransi'])->default('mandiri');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable();
            $table->text('allergies')->nullable();
            $table->timestamps();

            $table->index(['medical_record_number', 'name']);
            $table->index(['phone']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
