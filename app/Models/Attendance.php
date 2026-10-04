<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'break_duration_minutes',
        'total_work_minutes',
        'status',
        'verification_method',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getWorkingHoursAttribute(): string
    {
        if (!$this->clock_in || !$this->clock_out) {
            return '00:00';
        }

        $dateStr = $this->attendance_date instanceof \DateTimeInterface
            ? $this->attendance_date->format('Y-m-d')
            : substr((string) $this->attendance_date, 0, 10);

        try {
            $start = \Carbon\Carbon::parse($dateStr . ' ' . $this->clock_in);
            $end = \Carbon\Carbon::parse($dateStr . ' ' . $this->clock_out);
            $minutes = max(0, $end->diffInMinutes($start) - (int) ($this->break_duration_minutes ?? 0));
            return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
        } catch (\Exception $e) {
            return '00:00';
        }
    }

    public function getNetWorkMinutesAttribute(): int
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        $dateStr = $this->attendance_date instanceof \DateTimeInterface
            ? $this->attendance_date->format('Y-m-d')
            : substr((string) $this->attendance_date, 0, 10);

        try {
            $start = \Carbon\Carbon::parse($dateStr . ' ' . $this->clock_in);
            $end = \Carbon\Carbon::parse($dateStr . ' ' . $this->clock_out);
            return max(0, $end->diffInMinutes($start) - (int) ($this->break_duration_minutes ?? 0));
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getBreakDurationAttribute(): string
    {
        $minutes = (int) ($this->break_duration_minutes ?? 0);

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    public function getLateMinutesAttribute(): int
    {
        if (!$this->clock_in) return 0;
        
        $shiftStart = \App\Models\Setting::getVal('work_shift_start', '08:30:00');
        $grace = (int) \App\Models\Setting::getVal('grace_period', 30);
        
        $dateStr = $this->attendance_date instanceof \DateTimeInterface
            ? $this->attendance_date->format('Y-m-d')
            : substr((string) $this->attendance_date, 0, 10);
            
        try {
            $baseStart = \Carbon\Carbon::parse($dateStr . ' ' . $shiftStart);
            $expected = $baseStart->copy()->addMinutes($grace);
            $actual = \Carbon\Carbon::parse($dateStr . ' ' . $this->clock_in);
            
            if ($actual->greaterThan($expected)) {
                return $actual->diffInMinutes($baseStart);
            }
        } catch (\Exception $e) {
            return 0;
        }
        
        return 0;
    }
}