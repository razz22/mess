<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->enum('type', ['manager_reward', 'member_reward', 'report_points', 'best_market'])->default('member_reward');
            $table->decimal('points', 10, 2)->default(0);
            $table->string('gift_description')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_rewards');
    }
};
