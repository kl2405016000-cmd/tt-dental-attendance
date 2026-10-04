<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffDashboardController extends Controller
{
    protected function getEmployeeForUser($user)
    {
        return Employee::where('user_id', $user->id)->first()
            ?? Employee::where('email', $user->email)->first()
            ?? Employee::where('name', $user->name)->first();
    }

    public function index()
    {
        $user = Auth::user();
        $employee = $this->getEmployeeForUser($user);

        $today = Carbon::today();
        $attendance = $employee ? Attendance::where('employee_id', $employee->id)->whereDate('attendance_date', $today)->first() : null;

        $todayMinutes = 0;
        if ($attendance && $attendance->clock_in && $attendance->clock_out) {
            $dateStr = $attendance->attendance_date instanceof \DateTimeInterface
                ? $attendance->attendance_date->format('Y-m-d')
                : (string) $attendance->attendance_date;

            $start = Carbon::parse($dateStr . ' ' . $attendance->clock_in);
            $end = Carbon::parse($dateStr . ' ' . $attendance->clock_out);
            $todayMinutes = max(0, $end->diffInMinutes($start) - (int) ($attendance->break_duration_minutes ?? 0));
        }

        $recentAttendances = $employee
            ? Attendance::where('employee_id', $employee->id)->orderByDesc('attendance_date')->take(7)->get()
            : collect();

        return view('staff.dashboard', compact('employee', 'attendance', 'todayMinutes', 'recentAttendances'));
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $employee = $this->getEmployeeForUser($user);

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $status = $request->query('status');

        $query = $employee
            ? Attendance::where('employee_id', $employee->id)
            : Attendance::whereRaw('1 = 0');

        if ($dateFrom) {
            $query->whereDate('attendance_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('attendance_date', '<=', $dateTo);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->orderByDesc('attendance_date')->get();

        return view('staff.history', compact('attendances', 'employee', 'dateFrom', 'dateTo', 'status'));
    }

    public function clockIn(Request $request)
    {
        abort(403, 'Face verification is required for attendance actions. Please use the Face Scan Attendance kiosk.');
    }

    public function clockOut(Request $request)
    {
        abort(403, 'Face verification is required for attendance actions. Please use the Face Scan Attendance kiosk.');
    }

    public function startBreak(Request $request)
    {
        abort(403, 'Face verification is required for attendance actions. Please use the Face Scan Attendance kiosk.');
    }

    public function endBreak(Request $request)
    {
        abort(403, 'Face verification is required for attendance actions. Please use the Face Scan Attendance kiosk.');
    }
}
