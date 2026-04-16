<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mess_meal_types', function (Blueprint $table) {
            $table->unsignedTinyInteger('close_days_before')->default(0)->after('close_time')
                ->comment('How many days before the meal date the booking closes (0 = same day, 1 = previous day, etc.)');
        });
    }

    public function down(): void
    {
        Schema::table('mess_meal_types', function (Blueprint $table) {
            $table->dropColumn('close_days_before');
        });
    }
};
