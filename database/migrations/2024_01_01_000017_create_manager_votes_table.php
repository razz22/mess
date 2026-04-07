<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->foreignId('rotation_id')->constrained('manager_rotations')->onDelete('cascade');
            $table->foreignId('voter_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['rotation_id', 'voter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_votes');
    }
};
