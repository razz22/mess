<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mess_meal_routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->unsignedTinyInteger('week_no');   // 1-4 (week of month)
            $table->unsignedTinyInteger('day_of_week'); // 0=Sun,1=Mon,...,6=Sat
            $table->text('items');                    // comma-separated or free text
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['mess_id', 'week_no', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_meal_routines');
    }
};
