<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mess_subscriptions', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('mess_id')
                ->constrained('subscription_plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mess_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
    }
};
