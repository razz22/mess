<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mess_members', function (Blueprint $table) {
            $table->decimal('house_rent', 10, 2)->default(0)->after('carry_forward')
                ->comment('Monthly room/house rent amount');
            $table->decimal('advance_amount', 10, 2)->default(0)->after('house_rent')
                ->comment('Advance deposit collected from member');
            $table->date('advance_date')->nullable()->after('advance_amount');
            $table->text('notes')->nullable()->after('advance_date')
                ->comment('Internal manager notes about the member');
        });
    }

    public function down(): void
    {
        Schema::table('mess_members', function (Blueprint $table) {
            $table->dropColumn(['house_rent', 'advance_amount', 'advance_date', 'notes']);
        });
    }
};
