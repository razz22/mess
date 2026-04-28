<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('label');                          // e.g. "Ramadan Special"
            $table->unsignedSmallInteger('max_members');
            $table->decimal('price', 10, 2)->default(0);     // 0 = free
            $table->boolean('is_free')->default(false);
            $table->json('mess_ids');                         // assigned messes
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();      // null = indefinite
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_subscriptions');
    }
};
