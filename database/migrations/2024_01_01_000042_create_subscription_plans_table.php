<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->unsignedInteger('max_members')->default(30);
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedTinyInteger('duration_months')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('mess_upgrades', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('mess_id')
                ->constrained('subscription_plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mess_upgrades', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
        Schema::dropIfExists('subscription_plans');
    }
};
