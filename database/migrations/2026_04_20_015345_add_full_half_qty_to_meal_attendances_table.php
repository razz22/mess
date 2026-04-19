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
        Schema::table('meal_attendances', function (Blueprint $table) {
            $table->unsignedTinyInteger('full_qty')->default(0)->after('quantity');
            $table->unsignedTinyInteger('half_qty')->default(0)->after('full_qty');
        });

        // Backfill: derive full/half from existing quantity
        // quantity = full + half*0.5 → we treat whole part as full, remainder as half
        DB::statement('UPDATE meal_attendances SET full_qty = FLOOR(quantity), half_qty = ROUND((quantity - FLOOR(quantity)) * 2)');
    }

    public function down(): void
    {
        Schema::table('meal_attendances', function (Blueprint $table) {
            $table->dropColumn(['full_qty', 'half_qty']);
        });
    }
};
