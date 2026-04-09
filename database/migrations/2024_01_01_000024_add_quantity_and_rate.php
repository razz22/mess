<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Add quantity to meal_attendances
        Schema::table('meal_attendances', function (Blueprint $table) {
            $table->decimal('quantity', 4, 2)->default(1.00)->after('status');
        });

        // Add rate + allow toggling any meal type active
        Schema::table('mess_meal_types', function (Blueprint $table) {
            $table->decimal('rate', 10, 2)->default(0)->after('close_time');
        });
    }
    public function down(): void {
        Schema::table('meal_attendances', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
        Schema::table('mess_meal_types', function (Blueprint $table) {
            $table->dropColumn('rate');
        });
    }
};
