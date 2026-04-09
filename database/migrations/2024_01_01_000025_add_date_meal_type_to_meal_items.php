<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meal_items', function (Blueprint $table) {
            $table->date('date')->nullable()->after('mess_id');
            $table->string('meal_type')->nullable()->after('date'); // matches mess_meal_types.name
        });
    }

    public function down(): void
    {
        Schema::table('meal_items', function (Blueprint $table) {
            $table->dropColumn(['date', 'meal_type']);
        });
    }
};
