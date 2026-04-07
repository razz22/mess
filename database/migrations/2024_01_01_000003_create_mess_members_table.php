<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mess_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['owner', 'manager', 'author', 'member'])->default('member');
            $table->boolean('is_active')->default(true);
            $table->decimal('carry_forward', 10, 2)->default(0); // positive = extra credit, negative = debt
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['mess_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_members');
    }
};
