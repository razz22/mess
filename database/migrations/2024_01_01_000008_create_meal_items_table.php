<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->default('general'); // breakfast, lunch, dinner, snack, general
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo');
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_items');
    }
};
