<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mess_rent_fund_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->string('description', 300);
            $table->string('note', 500)->nullable();
            $table->date('transaction_date');
            $table->enum('source', ['surplus', 'expense', 'manual'])->default('manual');
            $table->foreignId('invoice_id')->nullable()->constrained('mess_rent_invoices')->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mess_rent_fund_transactions'); }
};
