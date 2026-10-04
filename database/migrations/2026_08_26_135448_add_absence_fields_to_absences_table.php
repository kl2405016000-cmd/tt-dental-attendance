<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('absences', 'employee_id')) {
            Schema::table('absences', function (Blueprint $table) {
                $table->foreignId('employee_id')->after('id')->constrained('employees')->cascadeOnDelete();
            });
        }

        if (!Schema::hasColumn('absences', 'absence_date')) {
            Schema::table('absences', function (Blueprint $table) {
                $table->date('absence_date')->after('employee_id');
            });
        }

        if (!Schema::hasColumn('absences', 'reason')) {
            Schema::table('absences', function (Blueprint $table) {
                $table->string('reason')->after('absence_date');
            });
        }

        if (!Schema::hasColumn('absences', 'remarks')) {
            Schema::table('absences', function (Blueprint $table) {
                $table->text('remarks')->nullable()->after('reason');
            });
        }
    }

    public function down(): void
    {
        // The original create migration owns these columns on fresh installs.
    }
};