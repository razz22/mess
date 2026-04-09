<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_monthly_summaries', function (Blueprint $table) {
            $table->boolean('exclude_from_shared')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('member_monthly_summaries', function (Blueprint $table) {
            $table->dropColumn('exclude_from_shared');
        });
    }
};
