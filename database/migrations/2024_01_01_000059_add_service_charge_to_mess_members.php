<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mess_members', function (Blueprint $table) {
            $table->decimal('service_charge', 10, 2)->default(0)->after('house_rent');
            $table->date('service_charge_date')->nullable()->after('service_charge');
        });
    }

    public function down(): void
    {
        Schema::table('mess_members', function (Blueprint $table) {
            $table->dropColumn(['service_charge', 'service_charge_date']);
        });
    }
};
