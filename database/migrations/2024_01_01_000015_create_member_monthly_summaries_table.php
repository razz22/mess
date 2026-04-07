<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_monthly_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('total_meal_days')->default(0);
            $table->decimal('meal_cost', 10, 2)->default(0);       // meal cost share
            $table->decimal('total_expenses', 10, 2)->default(0);  // shared expenses
            $table->decimal('market_expense', 10, 2)->default(0);  // market amount this member paid
            $table->decimal('total_deposit', 10, 2)->default(0);   // total deposited this month
            $table->decimal('carry_forward_in', 10, 2)->default(0); // brought from last month
            $table->decimal('total_payable', 10, 2)->default(0);   // total to pay
            $table->decimal('due_amount', 10, 2)->default(0);      // positive = due, negative = extra
            $table->decimal('carry_forward_out', 10, 2)->default(0); // carry to next month
            $table->enum('status', ['pending', 'settled'])->default('pending');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            $table->unique(['mess_id', 'user_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_monthly_summaries');
    }
};
