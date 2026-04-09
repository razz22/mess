<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_registration_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('mess_members')->onDelete('cascade');

            // Property location
            $table->string('flat_floor', 50)->nullable();
            $table->string('house_holding', 100)->nullable();
            $table->string('road', 100)->nullable();
            $table->string('area', 100)->nullable();
            $table->string('post_code', 20)->nullable();
            $table->string('division', 100)->nullable();
            $table->string('police_station', 100)->nullable();

            // Personal
            $table->string('tenant_name', 200)->nullable();
            $table->string('father_name', 200)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('marital_status', 30)->nullable(); // single, married, divorced, widowed
            $table->text('permanent_address')->nullable();

            // Professional
            $table->text('profession_workplace')->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('education', 100)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('nid_number', 50)->nullable();
            $table->string('passport_number', 50)->nullable();

            // Emergency contact
            $table->string('emergency_name', 200)->nullable();
            $table->string('emergency_relation', 100)->nullable();
            $table->text('emergency_address')->nullable();
            $table->string('emergency_mobile', 20)->nullable();

            // Family members (JSON: [{name, age, profession, mobile}])
            $table->json('family_members')->nullable();

            // Housekeeper
            $table->string('housekeeper_name', 200)->nullable();
            $table->string('housekeeper_nid', 50)->nullable();
            $table->string('housekeeper_mobile', 20)->nullable();
            $table->text('housekeeper_address')->nullable();

            // Driver
            $table->string('driver_name', 200)->nullable();
            $table->string('driver_nid', 50)->nullable();
            $table->string('driver_mobile', 20)->nullable();
            $table->text('driver_address')->nullable();

            // Previous landlord
            $table->string('prev_landlord_name', 200)->nullable();
            $table->string('prev_landlord_mobile', 20)->nullable();
            $table->text('prev_landlord_address')->nullable();
            $table->text('reason_leaving')->nullable();

            // Current landlord
            $table->string('curr_landlord_name', 200)->nullable();
            $table->string('curr_landlord_mobile', 20)->nullable();
            $table->date('living_since')->nullable();

            // Passport-size photo
            $table->string('passport_photo')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['mess_id', 'member_id']); // one form per member per mess
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_registration_forms');
    }
};
