<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE mess_rules MODIFY description MEDIUMTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE mess_rules MODIFY description TEXT NULL');
    }
};
