<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('blood_group', 5)->nullable()->after('nid_document')
                ->comment('A+, A-, B+, B-, AB+, AB-, O+, O-');
            $table->text('address')->nullable()->after('blood_group');
            $table->enum('occupation_type', ['student', 'employed', 'business', 'freelance', 'other'])
                ->nullable()->after('address');
            $table->string('organization', 255)->nullable()->after('occupation_type')
                ->comment('School, University, Office, Company name');
            $table->string('emergency_contact_name', 100)->nullable()->after('organization');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relation', 50)->nullable()->after('emergency_contact_phone');
            $table->date('date_of_birth')->nullable()->after('emergency_contact_relation');
            $table->string('gender', 10)->nullable()->after('date_of_birth');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'blood_group', 'address', 'occupation_type', 'organization',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
                'date_of_birth', 'gender',
            ]);
        });
    }
};
