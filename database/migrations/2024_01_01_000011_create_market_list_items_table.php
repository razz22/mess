<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routine_id')->constrained('market_routines')->onDelete('cascade');
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->string('item_name');
            $table->string('quantity')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('estimated_cost', 10, 2)->default(0);
            $table->decimal('actual_cost', 10, 2)->default(0);
            $table->boolean('purchased')->default(false);
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_list_items');
    }
};
