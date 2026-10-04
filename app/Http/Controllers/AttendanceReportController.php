<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::today()->format('Y-m-d');
        $dateTo = $request->date_to ?? Carbon::today()->format('Y-m-d');
        $employeeId = $request->employee_id;
        $status = $request->status;
        $verificationMethod = $request->verification_method;

        $query = Attendance::with('employee')
            ->whereBetween('attendance_date', [$dateFrom, $dateTo])
            ->orderByDesc('attendance_date')
            ->orderBy('clock_in');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($verificationMethod) {
            $query->where('verification_method', $verificationMethod);
        }

        $attendances = $query->get();
        $employees = Employee::orderBy('name')->get();

        return view('reports.attendance', compact(
            'attendances',
            'employees',
            'dateFrom',
            'dateTo',
            'employeeId',
            'status',
            'verificationMethod'
        ));
    }

    public function exportCsv(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::today()->format('Y-m-d');
        $dateTo = $request->date_to ?? Carbon::today()->format('Y-m-d');
        $employeeId = $request->employee_id;
        $status = $request->status;
        $verificationMethod = $request->verification_method;

        $query = Attendance::with('employee')
            ->whereBetween('attendance_date', [$dateFrom, $dateTo])
            ->orderByDesc('attendance_date')
            ->orderBy('clock_in');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($verificationMethod) {
            $query->where('verification_method', $verificationMethod);
        }

        $attendances = $query->get();
        $filename = 'attendance_report_' . $dateFrom . '_to_' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employee ID', 'Employee Name', 'Date', 'Clock In', 'Clock Out', 'Status', 'Verification Method']);

            foreach ($attendances as $row) {
                fputcsv($file, [
                    $row->employee->employee_id ?? 'N/A',
                    $row->employee->name ?? 'N/A',
                    $row->attendance_date,
                    $row->clock_in ?? '-',
                    $row->clock_out ?? '-',
                    $row->status,
                    $row->verification_method,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}