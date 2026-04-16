<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mess_show_causes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('mess_members')->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->string('subject', 200);
            $table->text('body');
            $table->text('member_reply')->nullable();
            $table->text('final_reply')->nullable();
            $table->enum('status', ['pending', 'replied', 'closed'])->default('pending');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('replied_at')->nullable();
            $table->timestamp('final_replied_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_show_causes');
    }
};
