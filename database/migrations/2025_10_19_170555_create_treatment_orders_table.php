<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('consultation_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['order_number', 'status']);
            $table->index(['patient_id', 'order_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_orders');
    }
};
