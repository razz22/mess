<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mess_members', function (Blueprint $table) {
            $table->string('room_no', 50)->nullable()->after('mess_id')
                ->comment('Room or unit number assigned to this member');
        });
    }

    public function down(): void
    {
        Schema::table('mess_members', function (Blueprint $table) {
            $table->dropColumn('room_no');
        });
    }
};
