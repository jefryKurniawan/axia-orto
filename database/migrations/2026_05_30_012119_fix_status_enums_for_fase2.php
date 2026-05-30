<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand treatment_orders enum to accept BOTH old and new values
        DB::statement("ALTER TABLE treatment_orders MODIFY COLUMN status ENUM('pending','in_progress','completed','cancelled','draft','confirmed','production','ready','delivered') DEFAULT 'draft'");

        // Step 2: Migrate existing data to new values
        DB::statement("UPDATE treatment_orders SET status = 'draft' WHERE status = 'pending'");
        DB::statement("UPDATE treatment_orders SET status = 'production' WHERE status = 'in_progress'");
        DB::statement("UPDATE treatment_orders SET status = 'delivered' WHERE status = 'completed'");

        // Step 3: Restrict to only new values
        DB::statement("ALTER TABLE treatment_orders MODIFY COLUMN status ENUM('draft','confirmed','production','ready','delivered','cancelled') DEFAULT 'draft'");

        // Step 4: Expand payments status enum, migrate, restrict
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','paid','failed','refunded','completed') DEFAULT 'pending'");
        DB::statement("UPDATE payments SET status = 'completed' WHERE status = 'paid'");
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','completed','failed','refunded') DEFAULT 'pending'");

        // Step 5: Rename payment column 'method' -> 'payment_method', add 'debit_card'
        DB::statement("ALTER TABLE payments CHANGE COLUMN method payment_method ENUM('cash','transfer','debit_card','credit_card') DEFAULT 'cash'");

        // Step 6: Add missing columns
        DB::statement("ALTER TABLE production_trackings ADD COLUMN completed_by BIGINT UNSIGNED NULL AFTER notes");
        DB::statement("ALTER TABLE payments ADD COLUMN created_by BIGINT UNSIGNED NULL AFTER notes");
    }

    public function down(): void
    {
        // Reverse treatment_orders
        DB::statement("ALTER TABLE treatment_orders MODIFY COLUMN status ENUM('draft','confirmed','production','ready','delivered','cancelled','pending','in_progress','completed') DEFAULT 'pending'");
        DB::statement("UPDATE treatment_orders SET status = 'pending' WHERE status = 'draft'");
        DB::statement("UPDATE treatment_orders SET status = 'in_progress' WHERE status = 'production'");
        DB::statement("UPDATE treatment_orders SET status = 'completed' WHERE status = 'delivered'");
        DB::statement("ALTER TABLE treatment_orders MODIFY COLUMN status ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending'");

        // Reverse payments
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','completed','failed','refunded','paid') DEFAULT 'pending'");
        DB::statement("UPDATE payments SET status = 'paid' WHERE status = 'completed'");
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','paid','failed','refunded') DEFAULT 'pending'");

        DB::statement("ALTER TABLE payments CHANGE COLUMN payment_method method ENUM('cash','transfer','credit_card') DEFAULT 'cash'");
        DB::statement("ALTER TABLE production_trackings DROP COLUMN completed_by");
        DB::statement("ALTER TABLE payments DROP COLUMN created_by");
    }
};
