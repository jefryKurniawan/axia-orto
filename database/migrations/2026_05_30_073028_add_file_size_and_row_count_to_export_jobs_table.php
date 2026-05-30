<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('export_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('file_size')->nullable()->after('file_path');
            $table->unsignedInteger('row_count')->nullable()->after('file_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('export_jobs', function (Blueprint $table) {
            $table->dropColumn(['file_size', 'row_count']);
        });
    }
};
