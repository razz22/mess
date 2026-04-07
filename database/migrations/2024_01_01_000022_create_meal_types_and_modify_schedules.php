<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dynamic meal types per mess
        Schema::create('mess_meal_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->string('name', 50);           // e.g. Breakfast, Lunch, Snacks
            $table->time('close_time')->nullable(); // attendance closes at this time
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['mess_id', 'name']);
        });

        // Change meal_schedules.type from enum to varchar
        Schema::table('meal_schedules', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_meal_types');
        Schema::table('meal_schedules', function (Blueprint $table) {
            $table->enum('type', ['breakfast', 'lunch', 'dinner'])->change();
        });
    }
};
