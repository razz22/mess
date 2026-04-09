<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_category_exclusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->timestamps();

            $table->unique(['mess_id', 'user_id', 'category_id', 'month', 'year'], 'member_cat_excl_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_category_exclusions');
    }
};
