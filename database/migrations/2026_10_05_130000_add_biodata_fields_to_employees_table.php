<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('ic_passport_number')->nullable()->after('name');
            $table->date('date_of_birth')->nullable()->after('ic_passport_number');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('phone');
            $table->date('date_joined')->nullable()->after('employment_status');
            $table->string('emergency_contact_name')->nullable()->after('date_joined');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_relationship');
            $table->text('emergency_contact_address')->nullable()->after('emergency_contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'ic_passport_number',
                'date_of_birth',
                'gender',
                'address',
                'date_joined',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'emergency_contact_address',
            ]);
        });
    }
};
