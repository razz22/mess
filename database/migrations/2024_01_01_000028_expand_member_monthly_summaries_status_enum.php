<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE member_monthly_summaries MODIFY COLUMN status ENUM('pending','settled','paid_out','carried_forward') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE member_monthly_summaries MODIFY COLUMN status ENUM('pending','settled') NOT NULL DEFAULT 'pending'");
    }
};
