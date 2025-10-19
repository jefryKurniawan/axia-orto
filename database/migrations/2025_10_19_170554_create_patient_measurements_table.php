<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('consultation_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('measurement_type');
            $table->decimal('value', 8, 2);
            $table->string('unit')->nullable();
            $table->timestamp('measured_at');
            $table->timestamps();

            $table->index(['patient_id', 'measurement_type']);
            $table->index(['consultation_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_measurements');
    }
};
