<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('absences') && Schema::hasColumn('absences', 'employee_id') && Schema::hasColumn('absences', 'absence_date')) {
            $hasUniqueIndex = collect(Schema::getIndexes('absences'))
                ->contains(fn (array $index): bool => $index['unique'] && $index['columns'] === ['employee_id', 'absence_date']);

            if (!$hasUniqueIndex) {
                Schema::table('absences', function (Blueprint $table) {
                    $table->unique(['employee_id', 'absence_date']);
                });
            }
        } elseif (!Schema::hasTable('absences')) {
            Schema::create('absences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('absence_date');
                $table->string('reason');
                $table->text('remarks')->nullable();
                $table->timestamps();
                $table->unique(['employee_id', 'absence_date']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('absences')) {
            $hasUniqueIndex = collect(Schema::getIndexes('absences'))
                ->contains(fn (array $index): bool => $index['unique'] && $index['columns'] === ['employee_id', 'absence_date']);

            if ($hasUniqueIndex) {
                Schema::table('absences', function (Blueprint $table) {
                    $table->dropUnique(['employee_id', 'absence_date']);
                });
            }
        }
    }
};