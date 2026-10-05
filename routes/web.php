<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FaceRegistrationController;
use App\Http\Controllers\FaceVerificationController;
use App\Http\Controllers\MedicalCertificateController;
use App\Http\Controllers\StaffDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Root & Authentication
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'staff.dashboard');
    }
    return redirect()->route('login');
});

// Single Staff-Oriented Login Page
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Legacy login redirects: no separate admin login exists
Route::redirect('/admin/login', '/login');
Route::redirect('/staff/login', '/login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/staff/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Password Reset
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/forgot-password/verify', [AuthController::class, 'showVerifyOtp'])->name('password.verify');
Route::post('/forgot-password/verify', [AuthController::class, 'verifyOtp'])->name('password.verify.otp');
Route::get('/forgot-password/reset', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/forgot-password/reset', [AuthController::class, 'updatePassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Biometric Attendance Kiosk (Public Scanner Terminal)
|--------------------------------------------------------------------------
*/
Route::get('/face-verification', [FaceVerificationController::class, 'index'])->name('face-verification');
Route::post('/face-verification/verify', [FaceVerificationController::class, 'verify'])->name('face-verification.verify');
Route::post('/attendance/status', [AttendanceController::class, 'status'])->name('attendance.status');
Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');
Route::post('/attendance/start-break', [AttendanceController::class, 'startBreak'])->name('attendance.startBreak');
Route::post('/attendance/end-break', [AttendanceController::class, 'endBreak'])->name('attendance.endBreak');

/*
|--------------------------------------------------------------------------
| Admin Portal (Role = admin ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Employees Management
    Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/admin/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/admin/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/admin/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/admin/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/admin/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::patch('/admin/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::delete('/admin/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/employees', fn () => redirect()->route('employees.index'));
    Route::get('/employees/create', fn () => redirect()->route('employees.create'));
    Route::get('/employees/{employee}', fn (\App\Models\Employee $employee) => redirect()->route('employees.show', $employee));

    // Attendance Management
    Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance', fn () => redirect()->route('attendance.index'));

    // Biometric & Face Registration
    Route::get('/admin/biometric', [FaceRegistrationController::class, 'index'])->name('admin.biometric');
    Route::get('/admin/face-registration', [FaceRegistrationController::class, 'index'])->name('face-attendance');
    Route::post('/admin/face-registration/register', [FaceRegistrationController::class, 'store'])->name('face-attendance.register');
    Route::get('/face-attendance', fn () => redirect()->route('face-attendance'));
    Route::post('/face-attendance/register', [FaceRegistrationController::class, 'store']);

    // Reports
    Route::get('/admin/reports/attendance', [AttendanceReportController::class, 'index'])->name('reports.attendance');
    Route::get('/admin/reports/attendance/export', [AttendanceReportController::class, 'exportCsv'])->name('reports.attendance.export');
    Route::get('/reports/attendance', fn () => redirect()->route('reports.attendance'));
    Route::get('/reports/attendance/export', fn () => redirect()->route('reports.attendance.export'));

    // Absences
    Route::get('/admin/absences', [AbsenceController::class, 'index'])->name('absences.index');
    Route::post('/admin/absences', [AbsenceController::class, 'store'])->name('absences.store');
    Route::delete('/admin/absences/{absence}', [AbsenceController::class, 'destroy'])->name('absences.destroy');
    Route::get('/absences', fn () => redirect()->route('absences.index'));
    Route::post('/absences', [AbsenceController::class, 'store']);
    Route::delete('/absences/{absence}', [AbsenceController::class, 'destroy']);

    // MC Management
    Route::get('/admin/mc', [MedicalCertificateController::class, 'adminIndex'])->name('admin.mc.index');
    Route::post('/admin/mc/{medicalCertificate}/approve', [MedicalCertificateController::class, 'approve'])->name('admin.mc.approve');
    Route::post('/admin/mc/{medicalCertificate}/reject', [MedicalCertificateController::class, 'reject'])->name('admin.mc.reject');
    Route::get('/admin/mc/{medicalCertificate}/download', [MedicalCertificateController::class, 'download'])->name('admin.mc.download');

    // Settings
    Route::get('/admin/settings', [AdminDashboardController::class, 'settings'])->name('settings.index');
    Route::post('/admin/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
    Route::get('/settings', fn () => redirect()->route('settings.index'));
});

/*
|--------------------------------------------------------------------------
| Staff Portal (Role = staff ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::post('/staff/clock-in', [StaffDashboardController::class, 'clockIn'])->name('staff.clockIn');
    Route::post('/staff/clock-out', [StaffDashboardController::class, 'clockOut'])->name('staff.clockOut');
    Route::post('/staff/start-break', [StaffDashboardController::class, 'startBreak'])->name('staff.startBreak');
    Route::post('/staff/end-break', [StaffDashboardController::class, 'endBreak'])->name('staff.endBreak');
    Route::get('/staff/history', [StaffDashboardController::class, 'history'])->name('staff.history');
    Route::get('/staff/mc', [MedicalCertificateController::class, 'staffIndex'])->name('staff.mc.index');
    Route::post('/staff/mc', [MedicalCertificateController::class, 'store'])->name('staff.mc.store');
    Route::get('/staff/mc/{medicalCertificate}/download', [MedicalCertificateController::class, 'staffDownload'])->name('staff.mc.download');
    Route::get('/staff/absences', [AbsenceController::class, 'staffIndex'])->name('staff.absences.index');
    Route::post('/staff/absences', [AbsenceController::class, 'staffStore'])->name('staff.absences.store');
    Route::delete('/staff/absences/{absence}', [AbsenceController::class, 'staffDestroy'])->name('staff.absences.destroy');
});
