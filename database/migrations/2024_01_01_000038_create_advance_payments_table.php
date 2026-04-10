<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('mess_members')->onDelete('cascade');
            $table->enum('transaction_type', ['received', 'refunded', 'adjusted'])
                ->default('received')
                ->comment('received=collected from tenant, refunded=returned to tenant, adjusted=applied to rent');
            $table->decimal('amount', 10, 2);
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['mess_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advance_payments');
    }
};
