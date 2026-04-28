<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('is_super_admin');
            $table->timestamp('blocked_at')->nullable()->after('is_blocked');
            $table->timestamp('blocked_until')->nullable()->after('blocked_at'); // null = permanent
            $table->string('block_reason')->nullable()->after('blocked_until');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_blocked', 'blocked_at', 'blocked_until', 'block_reason']);
        });
    }
};
