<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_monthly_summaries', function (Blueprint $table) {
            $table->decimal('total_meal_days', 8, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('member_monthly_summaries', function (Blueprint $table) {
            $table->unsignedInteger('total_meal_days')->default(0)->change();
        });
    }
};
