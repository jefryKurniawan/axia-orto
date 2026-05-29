<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cache_versions', function (Blueprint $table) {
            $table->string('module_name', 50)->primary();
            $table->unsignedInteger('version')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_versions');
    }
};
