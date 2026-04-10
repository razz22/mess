<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('mess_members')->onDelete('cascade');
            $table->unsignedTinyInteger('month');  // 1-12
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_type', ['rent', 'penalty', 'discount', 'adjustment'])
                ->default('rent')
                ->comment('rent=normal payment, penalty=late fee, discount=waiver, adjustment=manual correction');
            $table->enum('payment_method', ['cash', 'bkash', 'nagad', 'bank_transfer', 'other'])
                ->default('cash');
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['mess_id', 'month', 'year']);
            $table->index(['member_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_payments');
    }
};
