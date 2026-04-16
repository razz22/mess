<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Drop FK, old unique, add column, add new unique, re-add FK
        DB::statement('ALTER TABLE mess_meal_routines DROP FOREIGN KEY mess_meal_routines_mess_id_foreign');
        DB::statement('ALTER TABLE mess_meal_routines DROP INDEX mess_meal_routines_mess_id_week_no_day_of_week_unique');
        DB::statement('ALTER TABLE mess_meal_routines ADD COLUMN meal_type VARCHAR(80) NOT NULL DEFAULT "" AFTER mess_id');
        DB::statement('ALTER TABLE mess_meal_routines ADD UNIQUE KEY mroutine_unique (mess_id, meal_type, week_no, day_of_week)');
        DB::statement('ALTER TABLE mess_meal_routines ADD CONSTRAINT mess_meal_routines_mess_id_foreign FOREIGN KEY (mess_id) REFERENCES messes(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE mess_meal_routines DROP FOREIGN KEY mess_meal_routines_mess_id_foreign');
        DB::statement('ALTER TABLE mess_meal_routines DROP INDEX mroutine_unique');
        DB::statement('ALTER TABLE mess_meal_routines DROP COLUMN meal_type');
        DB::statement('ALTER TABLE mess_meal_routines ADD UNIQUE KEY mess_meal_routines_mess_id_week_no_day_of_week_unique (mess_id, week_no, day_of_week)');
        DB::statement('ALTER TABLE mess_meal_routines ADD CONSTRAINT mess_meal_routines_mess_id_foreign FOREIGN KEY (mess_id) REFERENCES messes(id) ON DELETE CASCADE');
    }
};
