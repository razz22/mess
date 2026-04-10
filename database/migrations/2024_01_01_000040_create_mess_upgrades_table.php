<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mess_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // requester (owner)
            $table->unsignedInteger('current_limit');
            $table->unsignedInteger('requested_limit');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('bkash_number', 20)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // Allow super admin to control per-user mess creation limit
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_messes')->default(2)->after('is_super_admin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_upgrades');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('max_messes');
        });
    }
};
