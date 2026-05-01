<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manager_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nominated_by')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->timestamps();
            $table->unique(['mess_id', 'user_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_nominations');
    }
};
