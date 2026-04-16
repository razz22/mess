<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mess_rent_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->string('invoice_no', 50)->unique();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('house_owner_name', 150);     // landlord name
            $table->string('house_owner_phone', 30)->nullable();
            $table->string('property_address', 500)->nullable();
            $table->decimal('rent_amount', 10, 2);       // amount to pay landlord
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('notes', 500)->nullable();
            $table->enum('status', ['draft', 'paid', 'cancelled'])->default('draft');
            $table->foreignId('issued_by')->constrained('users');
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mess_rent_invoices'); }
};
