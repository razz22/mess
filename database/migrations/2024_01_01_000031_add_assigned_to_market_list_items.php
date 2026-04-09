<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('market_list_items', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('added_by')
                ->constrained('users')->nullOnDelete();
            $table->date('expense_date')->nullable()->after('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('market_list_items', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['assigned_to', 'expense_date']);
        });
    }
};
