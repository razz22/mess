<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add a plain mess_id index first so MySQL FK constraint stays satisfied
        DB::statement('ALTER TABLE manager_rotations ADD INDEX idx_mr_mess_id (mess_id)');
        DB::statement('ALTER TABLE manager_rotations DROP INDEX manager_rotations_mess_id_month_year_unique');
        DB::statement('ALTER TABLE manager_rotations ADD UNIQUE INDEX manager_rotations_mess_month_year_user_unique (mess_id, month, year, user_id)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE manager_rotations DROP INDEX manager_rotations_mess_month_year_user_unique');
        DB::statement('ALTER TABLE manager_rotations ADD UNIQUE INDEX manager_rotations_mess_id_month_year_unique (mess_id, month, year)');
        DB::statement('ALTER TABLE manager_rotations DROP INDEX idx_mr_mess_id');
    }
};
