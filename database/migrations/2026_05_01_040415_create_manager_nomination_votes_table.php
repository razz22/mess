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
        Schema::create('manager_nomination_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('manager_nominations')->cascadeOnDelete();
            $table->foreignId('voter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mess_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['nomination_id', 'voter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_nomination_votes');
    }
};
