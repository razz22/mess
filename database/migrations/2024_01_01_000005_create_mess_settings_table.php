<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mess_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade')->unique();
            $table->time('breakfast_close')->default('09:00:00');
            $table->time('lunch_close')->default('14:00:00');
            $table->time('dinner_close')->default('21:00:00');
            $table->decimal('monthly_rate', 10, 2)->default(0); // per person meal rate
            $table->boolean('allow_meal_off')->default(true);
            $table->boolean('auto_meal_on')->default(true); // auto mark meal ON if not marked
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_settings');
    }
};
