<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['mess_id']);
            $table->dropColumn('mess_id');
            $table->json('mess_ids')->nullable()->after('audience');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('mess_ids');
            $table->foreignId('mess_id')->nullable()->constrained('messes')->cascadeOnDelete();
        });
    }
};
