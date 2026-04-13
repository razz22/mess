<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mess_meal_types', function (Blueprint $table) {
            $table->boolean('closes_previous_day')->default(false)->after('close_time');
        });
    }

    public function down(): void
    {
        Schema::table('mess_meal_types', function (Blueprint $table) {
            $table->dropColumn('closes_previous_day');
        });
    }
};
