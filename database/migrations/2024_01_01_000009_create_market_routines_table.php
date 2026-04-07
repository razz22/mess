<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->date('date');
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'exchanged', 'skipped'])->default('pending');
            $table->text('notes')->nullable();
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['mess_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_routines');
    }
};
