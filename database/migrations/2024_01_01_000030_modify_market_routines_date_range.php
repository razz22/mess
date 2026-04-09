<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add start_date and end_date columns
        Schema::table('market_routines', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('mess_id');
            $table->date('end_date')->nullable()->after('start_date');
        });

        // Copy existing date → start_date and end_date
        DB::statement('UPDATE market_routines SET start_date = `date`, end_date = `date`');

        // Make non-nullable now that data is set
        Schema::table('market_routines', function (Blueprint $table) {
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
        });

        // Drop old unique constraint and date column
        // Must drop FK first, then unique index, then re-add FK
        Schema::table('market_routines', function (Blueprint $table) {
            $table->dropForeign('market_routines_mess_id_foreign');
            $table->dropUnique('market_routines_mess_id_date_unique');
            $table->dropColumn('date');
            $table->foreign('mess_id')->references('id')->on('messes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('market_routines', function (Blueprint $table) {
            $table->date('date')->nullable()->after('mess_id');
        });
        DB::statement('UPDATE market_routines SET `date` = start_date');
        Schema::table('market_routines', function (Blueprint $table) {
            $table->date('date')->nullable(false)->change();
            $table->unique(['mess_id', 'date']);
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
