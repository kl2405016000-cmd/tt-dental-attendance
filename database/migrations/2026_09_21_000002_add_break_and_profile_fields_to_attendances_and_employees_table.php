<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('department')->nullable()->after('position');
            $table->string('employment_status')->default('active')->after('department');
            $table->string('profile_photo')->nullable()->after('employment_status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->time('break_start')->nullable()->after('clock_in');
            $table->time('break_end')->nullable()->after('break_start');
            $table->integer('break_duration_minutes')->default(0)->after('break_end');
            $table->integer('total_work_minutes')->nullable()->after('break_duration_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['department', 'employment_status', 'profile_photo']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['break_start', 'break_end', 'break_duration_minutes', 'total_work_minutes']);
        });
    }
};
