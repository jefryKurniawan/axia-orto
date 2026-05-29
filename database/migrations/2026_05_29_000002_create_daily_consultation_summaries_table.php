<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_consultation_summaries', function (Blueprint $table) {
            $table->date('date')->primary();
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('scheduled')->default(0);
            $table->unsignedInteger('in_progress')->default(0);
            $table->unsignedInteger('completed')->default(0);
            $table->unsignedInteger('cancelled')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_consultation_summaries');
    }
};
