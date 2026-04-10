<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the enum constraint and change to varchar so any plan name works
        DB::statement("ALTER TABLE mess_subscriptions MODIFY plan VARCHAR(100) NOT NULL DEFAULT 'basic'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE mess_subscriptions MODIFY plan ENUM('basic','standard','premium') NOT NULL DEFAULT 'basic'");
    }
};
