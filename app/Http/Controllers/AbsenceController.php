<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    /**
     * Helper to resolve the employee record for the authenticated user.
     */
    protected function getEmployeeForUser($user)
    {
        if (!$user) {
            return null;
        }

        return Employee::where('user_id', $user->id)->first()
            ?? Employee::where('email', $user->email)->first()
            ?? Employee::where('name', $user->name)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN PORTAL: Display Absences & Oversight
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        // Selected date for daily absence inspection
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        // All active employees
        $employees = Employee::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        // Employees with attendance on the selected date
        $presentEmployeeIds = Attendance::whereDate('attendance_date', $date->toDateString())
            ->whereNotNull('clock_in')
            ->pluck('employee_id')
            ->toArray();

        // Employees who did not clock in on the selected date
        $absentEmployees = $employees->filter(function ($employee) use ($presentEmployeeIds) {
            return !in_array($employee->id, $presentEmployeeIds);
        });

        // Absence records specifically for the inspected date (keyed by employee_id)
        try {
            $absenceRecords = Absence::whereDate('absence_date', $date->toDateString())
                ->get()
                ->keyBy('employee_id');
        } catch (\Exception $e) {
            $absenceRecords = collect();
        }

        // Comprehensive list of all absence records across all employees
        try {
            $allAbsences = Absence::with('employee')
                ->orderByDesc('absence_date')
                ->orderByDesc('created_at')
                ->get();
        } catch (\Exception $e) {
            $allAbsences = collect();
        }

        return view('absences.index', compact('date', 'employees', 'absentEmployees', 'absenceRecords', 'allAbsences'));
    }

    /**
     * ADMIN: Save / Override Absence Reason
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'absence_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if attendance record exists with clock-in
        $attendanceExists = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('attendance_date', $validated['absence_date'])
            ->whereNotNull('clock_in')
            ->exists();

        if ($attendanceExists) {
            return back()->with('error', 'This employee already has a clock-in attendance record for this date.');
        }

        Absence::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'absence_date' => $validated['absence_date'],
            ],
            [
                'reason' => $validated['reason'],
                'remarks' => $validated['remarks'] ?? null,
            ]
        );

        return back()->with('success', 'Absence record saved successfully.');
    }

    /**
     * ADMIN: Remove Absence Record
     */
    public function destroy(Absence $absence)
    {
        $absence->delete();

        return back()->with('success', 'Absence record removed successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | STAFF PORTAL: My Absences & Staff Submission
    |--------------------------------------------------------------------------
    */

    /**
     * STAFF: Show logged-in staff member's absence page and history
     */
    public function staffIndex(Request $request)
    {
        $user = Auth::user();
        $employee = $this->getEmployeeForUser($user);

        $records = $employee
            ? Absence::where('employee_id', $employee->id)->orderByDesc('absence_date')->get()
            : collect();

        return view('staff.absences', compact('employee', 'records'));
    }

    /**
     * STAFF: Submit an absence report for the logged-in staff member only
     */
    public function staffStore(Request $request)
    {
        $user = Auth::user();
        $employee = $this->getEmployeeForUser($user);

        if (!$employee) {
            return back()->with('error', 'Your user account is not linked to an employee profile. Please contact an administrator.');
        }

        $validated = $request->validate([
            'absence_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        // Prevent submitting an absence if the staff already clocked in for this day
        $clockInExists = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $validated['absence_date'])
            ->whereNotNull('clock_in')
            ->exists();

        if ($clockInExists) {
            return back()->with('error', 'You already have an active attendance clock-in recorded for ' . Carbon::parse($validated['absence_date'])->format('d M Y') . '.');
        }

        // Always force the logged-in employee ID (prevents submitting for another user)
        Absence::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'absence_date' => $validated['absence_date'],
            ],
            [
                'reason' => $validated['reason'],
                'remarks' => $validated['remarks'] ?? null,
            ]
        );

        return redirect()->route('staff.absences.index')->with('success', 'Your absence report has been submitted successfully.');
    }

    /**
     * STAFF: Remove own absence record
     */
    public function staffDestroy(Absence $absence)
    {
        $user = Auth::user();
        $employee = $this->getEmployeeForUser($user);

        if (!$employee || $absence->employee_id !== $employee->id) {
            abort(403, 'Unauthorized action. You can only manage your own absence records.');
        }

        $absence->delete();

        return back()->with('success', 'Your absence report has been removed.');
    }
}