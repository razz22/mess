<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->date('date');
            $table->enum('type', ['breakfast', 'lunch', 'dinner']);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->decimal('meal_cost', 10, 2)->default(0); // total cost for this meal
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->unique(['mess_id', 'date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_schedules');
    }
};
