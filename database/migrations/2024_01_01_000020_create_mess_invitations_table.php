<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mess_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->string('email');
            $table->string('token')->unique();
            $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending');
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_invitations');
    }
};
