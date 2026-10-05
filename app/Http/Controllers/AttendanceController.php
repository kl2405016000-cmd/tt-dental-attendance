<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected function validateStaffAccess(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isStaff()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $loggedInEmployee = \App\Models\Employee::where('user_id', $user->id)->first()
                ?? \App\Models\Employee::where('email', $user->email)->first()
                ?? \App\Models\Employee::where('name', $user->name)->first();

            if (!$loggedInEmployee || (int) $request->employee_id !== (int) $loggedInEmployee->id) {
                return false;
            }
        }
        return true;
    }

    protected function getLocationValidation(Request $request): array
    {
        // Allow Admin manual attendance override from the web portal
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isAdmin() && !$request->expectsJson()) {
            return [
                'valid' => true,
                'error' => null,
                'matched_location' => ['name' => 'Admin Override', 'distance' => 0, 'radius' => 0],
                'nearest_location' => null,
                'distances' => [],
            ];
        }

        $lat = $request->input('lat');
        $lng = $request->input('lng');

        return \App\Services\GeolocationService::validateLocation($lat, $lng);
    }

    protected function getLocationError(Request $request): ?string
    {
        $validation = $this->getLocationValidation($request);
        return $validation['error'];
    }

    protected function validateLocation(Request $request): bool
    {
        return $this->getLocationError($request) === null;
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $employees = Employee::where('status', true)
            ->orderBy('name')
            ->get();

        $attendances = Attendance::with('employee')
            ->whereDate(
                'attendance_date',
                Carbon::today()
            )
            ->orderByDesc('created_at')
            ->get();

        return view(
            'attendance.index',
            compact(
                'employees',
                'attendances'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Attendance Status
    |--------------------------------------------------------------------------
    */

    public function status(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        if (!$this->validateStaffAccess($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized attendance access for this employee.'], 403);
        }

        $today = Carbon::today();

        $attendance = Attendance::where(
                'employee_id',
                $request->employee_id
            )
            ->whereDate(
                'attendance_date',
                $today
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | No attendance yet
        |--------------------------------------------------------------------------
        */

        if (!$attendance) {
            return response()->json([
                'success' => true,
                'status' => 'not_clocked_in',
                'message' => 'Employee has not clocked in today.',
                'attendance' => null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Break Status
        |--------------------------------------------------------------------------
        */

        if ($attendance->break_start && !$attendance->break_end) {
            return response()->json([
                'success' => true,
                'status' => 'on_break',
                'message' => 'Employee is currently on break.',
                'attendance' => [
                    'id' => $attendance->id,
                    'clock_in' => $attendance->clock_in,
                    'clock_out' => null,
                    'status' => $attendance->status,
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Clocked in but not clocked out (and not on break)
        |--------------------------------------------------------------------------
        */

        if (!$attendance->clock_out) {
            return response()->json([
                'success' => true,
                'status' => 'clocked_in',
                'message' => 'Employee has already clocked in. Break and Clock Out are available.',
                'attendance' => [
                    'id' => $attendance->id,
                    'clock_in' => $attendance->clock_in,
                    'clock_out' => null,
                    'status' => $attendance->status,
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Already completed attendance
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'status' => 'completed',
            'message' => 'Employee has already completed attendance today.',
            'attendance' => [
                'id' => $attendance->id,
                'clock_in' => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
                'status' => $attendance->status,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Clock In
    |--------------------------------------------------------------------------
    */

    public function clockIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        if (!$this->validateStaffAccess($request)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized attendance access for this employee.'], 403);
            }
            return back()->with('error', 'Unauthorized attendance access for this employee.');
        }

        $locationError = $this->getLocationError($request);
        if ($locationError) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $locationError], 403);
            }
            return back()->with('error', $locationError);
        }

        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Check today's attendance
        |--------------------------------------------------------------------------
        */

        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('attendance_date', $today)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Already clocked in
        |--------------------------------------------------------------------------
        */

        if ($attendance) {
            if ($attendance->clock_out) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'status' => 'completed',
                        'message' => 'This employee has already completed attendance today.',
                    ], 422);
                }
                return back()->with('error', 'This employee has already completed attendance today.');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status' => 'clocked_in',
                    'message' => 'This employee has already clocked in today. Please use Clock Out.',
                    'attendance' => [
                        'id' => $attendance->id,
                        'clock_in' => $attendance->clock_in,
                        'clock_out' => $attendance->clock_out,
                    ],
                ], 422);
            }

            return back()->with('error', 'This employee has already clocked in today. Please use Clock Out.');
        }

        /*
        |--------------------------------------------------------------------------
        | Create Attendance
        |--------------------------------------------------------------------------
        */

        $attendance = Attendance::create([
            'employee_id' => $request->employee_id,
            'attendance_date' => $today,
            'clock_in' => Carbon::now()->format('H:i:s'),
            'status' => 'Present',
            'verification_method' => $request->input('verification_method', $request->expectsJson() ? 'Biometric' : 'Manual'),
        ]);

        $employee = Employee::find($request->employee_id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'clocked_in',
                'message' => 'Employee clocked in successfully.',
                'attendance' => [
                    'id' => $attendance->id,
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'clock_in' => $attendance->clock_in,
                    'clock_out' => null,
                    'status' => $attendance->status,
                    'verification_method' => $attendance->verification_method,
                ],
            ]);
        }

        return back()->with('success', 'Employee clocked in successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | Clock Out
    |--------------------------------------------------------------------------
    */

    public function clockOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        if (!$this->validateStaffAccess($request)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized attendance access for this employee.'], 403);
            }
            return back()->with('error', 'Unauthorized attendance access for this employee.');
        }

        $locationError = $this->getLocationError($request);
        if ($locationError) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $locationError], 403);
            }
            return back()->with('error', $locationError);
        }

        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status' => 'not_clocked_in',
                    'message' => 'This employee has not clocked in today.',
                ], 422);
            }
            return back()->with('error', 'This employee has not clocked in today.');
        }

        if ($attendance->clock_out) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status' => 'completed',
                    'message' => 'This employee has already clocked out today.',
                ], 422);
            }
            return back()->with('error', 'This employee has already clocked out today.');
        }

        $clockOutTime = Carbon::now()->format('H:i:s');
        $totalMinutes = $attendance->clock_in
            ? max(0, Carbon::parse($attendance->clock_in)->diffInMinutes(Carbon::parse($clockOutTime)) - (int) ($attendance->break_duration_minutes ?? 0))
            : 0;

        $attendance->update([
            'clock_out' => $clockOutTime,
            'status' => 'Present',
            'total_work_minutes' => $totalMinutes,
        ]);

        $employee = Employee::find($request->employee_id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'completed',
                'message' => 'Employee clocked out successfully.',
                'attendance' => [
                    'id' => $attendance->id,
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'clock_in' => $attendance->clock_in,
                    'clock_out' => $attendance->clock_out,
                    'status' => $attendance->status,
                    'verification_method' => $attendance->verification_method,
                ],
            ]);
        }

        return back()->with('success', 'Employee clocked out successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Start Break
    |--------------------------------------------------------------------------
    */

    public function startBreak(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        if (!$this->validateStaffAccess($request)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized attendance access for this employee.'], 403);
            }
            return back()->with('error', 'Unauthorized attendance access for this employee.');
        }

        $locationError = $this->getLocationError($request);
        if ($locationError) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $locationError], 403);
            }
            return back()->with('error', $locationError);
        }

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'You must clock in before starting a break.'], 422);
            }
            return back()->with('error', 'You must clock in before starting a break.');
        }

        if ($attendance->break_start && !$attendance->break_end) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'You are already on a break.'], 422);
            }
            return back()->with('error', 'You are already on a break.');
        }

        if ($attendance->clock_out) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Attendance is already completed for today.'], 422);
            }
            return back()->with('error', 'Attendance is already completed for today.');
        }

        $attendance->break_start = Carbon::now()->format('H:i:s');
        $attendance->break_end = null;
        $attendance->status = 'On Break';
        $attendance->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'on_break',
                'message' => 'Break started successfully.',
                'attendance' => $attendance,
            ]);
        }

        return back()->with('success', 'Break started successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | End Break
    |--------------------------------------------------------------------------
    */

    public function endBreak(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        if (!$this->validateStaffAccess($request)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized attendance access for this employee.'], 403);
            }
            return back()->with('error', 'Unauthorized attendance access for this employee.');
        }

        $locationError = $this->getLocationError($request);
        if ($locationError) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $locationError], 403);
            }
            return back()->with('error', $locationError);
        }

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->break_start || $attendance->break_end !== null) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No active break to end.'], 422);
            }
            return back()->with('error', 'No active break to end.');
        }

        $breakStart = Carbon::parse($attendance->break_start);
        $breakEnd = Carbon::now();
        $durationMinutes = $breakStart->diffInMinutes($breakEnd);

        $attendance->break_end = $breakEnd->format('H:i:s');
        $attendance->break_duration_minutes = (int) ($attendance->break_duration_minutes ?? 0) + $durationMinutes;
        $attendance->status = 'Present';
        $attendance->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'clocked_in',
                'message' => 'Break ended successfully.',
                'attendance' => $attendance,
            ]);
        }

        return back()->with('success', 'Break ended successfully.');
    }
}