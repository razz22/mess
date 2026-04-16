<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Named funds for storing surplus money
        Schema::create('mess_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('description', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('mess_fund_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained('mess_funds')->cascadeOnDelete();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->string('source', 50)->default('manual');
            $table->string('notes', 500)->nullable();
            $table->date('transaction_date');
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });

        // Direct surplus expenses tied to a rent invoice (no fund needed)
        Schema::create('mess_rent_invoice_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('mess_rent_invoices')->cascadeOnDelete();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->string('description', 500);
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_rent_invoice_expenses');
        Schema::dropIfExists('mess_fund_transactions');
        Schema::dropIfExists('mess_funds');
    }
};
