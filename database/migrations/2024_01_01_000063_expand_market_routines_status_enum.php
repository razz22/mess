<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE market_routines MODIFY COLUMN status ENUM('pending','approved','pending_reapproval','completed','exchanged','skipped') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("UPDATE market_routines SET status = 'completed' WHERE status IN ('approved','pending_reapproval')");
        DB::statement("ALTER TABLE market_routines MODIFY COLUMN status ENUM('pending','completed','exchanged','skipped') NOT NULL DEFAULT 'pending'");
    }
};
