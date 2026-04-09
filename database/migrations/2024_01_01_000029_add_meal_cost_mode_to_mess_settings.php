<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mess_settings', function (Blueprint $table) {
            $table->enum('meal_cost_mode', ['monthly', 'daily'])->default('monthly')->after('monthly_rate');
        });
    }

    public function down(): void
    {
        Schema::table('mess_settings', function (Blueprint $table) {
            $table->dropColumn('meal_cost_mode');
        });
    }
};
