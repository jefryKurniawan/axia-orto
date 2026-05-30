<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_revenue_summaries', function (Blueprint $table) {
            $table->date('date')->primary();
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('cash_revenue', 15, 2)->default(0);
            $table->decimal('transfer_revenue', 15, 2)->default(0);
            $table->decimal('card_revenue', 15, 2)->default(0);
            $table->unsignedInteger('total_transactions')->default(0);
            $table->unsignedInteger('completed_transactions')->default(0);
            $table->unsignedInteger('pending_transactions')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_revenue_summaries');
    }
};
