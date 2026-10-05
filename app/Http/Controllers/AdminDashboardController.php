<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\MedicalCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $employees = Employee::orderBy('name')->get();
        $totalEmployees = $employees->where('status', true)->count();
        $todayAttendances = Attendance::with('employee')->whereDate('attendance_date', $today)->get();
        $presentToday = $todayAttendances->whereNotNull('clock_in')->pluck('employee_id')->unique()->count();
        $onLeave = Attendance::whereDate('attendance_date', $today)->where('status', 'like', '%Leave%')->count()
            + MedicalCertificate::whereDate('attendance_date', $today)->where('status', 'approved')->count();
        $absentToday = max($totalEmployees - $presentToday - $onLeave, 0);
        $lateToday = $todayAttendances->filter(fn ($attendance) => $attendance->clock_in && $attendance->clock_in > '09:00:00')->pluck('employee_id')->unique()->count();
        $onBreak = $todayAttendances->filter(fn ($attendance) => $attendance->break_start && !$attendance->break_end)->count();
        $totalClockIns = $todayAttendances->whereNotNull('clock_in')->count();
        $totalClockOuts = $todayAttendances->whereNotNull('clock_out')->count();
        $attendanceRate = $totalEmployees ? round(($presentToday / $totalEmployees) * 100) : 0;
        $recentAttendances = Attendance::with('employee')->whereDate('attendance_date', $today)->orderByDesc('clock_in')->take(8)->get();
        $pendingMc = MedicalCertificate::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'employees',
            'todayAttendances',
            'totalEmployees',
            'presentToday',
            'absentToday',
            'lateToday',
            'onBreak',
            'onLeave',
            'totalClockIns',
            'totalClockOuts',
            'attendanceRate',
            'recentAttendances',
            'pendingMc'
        ));
    }

    public function settings()
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Sync legacy workplace geofence keys with Location 1 for backward compatibility
        if (isset($data['location_1_lat'])) {
            \App\Models\Setting::updateOrCreate(['key' => 'workplace_lat'], ['value' => $data['location_1_lat']]);
            \App\Models\Setting::updateOrCreate(['key' => 'workplace_lng'], ['value' => $data['location_1_lng']]);
            \App\Models\Setting::updateOrCreate(['key' => 'workplace_radius'], ['value' => $data['location_1_radius'] ?? '500']);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
