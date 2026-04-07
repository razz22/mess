<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('meal_attendance_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('log_date');
            $table->unsignedTinyInteger('changes')->default(0);
            $table->timestamps();
            $table->unique(['mess_id', 'user_id', 'log_date']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('meal_attendance_change_logs');
    }
};
