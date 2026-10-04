<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\MedicalCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalCertificateController extends Controller
{
    public function staffIndex()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first()
            ?? Employee::where('email', $user->email)->first()
            ?? Employee::where('name', $user->name)->first();

        $records = $employee ? MedicalCertificate::where('employee_id', $employee->id)->orderByDesc('attendance_date')->get() : collect();

        return view('staff.mc', compact('records'));
    }

    public function adminIndex()
    {
        $records = MedicalCertificate::with('employee')->orderByDesc('attendance_date')->get();

        return view('admin.mc', compact('records'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first()
            ?? Employee::where('email', $user->email)->first()
            ?? Employee::where('name', $user->name)->first();

        if (!$employee) {
            return back()->with('error', 'Employee profile not found.');
        }

        $validated = $request->validate([
            'attendance_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'min:3', 'max:255'],
            'attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $path = $request->file('attachment')->store('mc-uploads', 'public');

        MedicalCertificate::create([
            'employee_id' => $employee->id,
            'attendance_date' => $validated['attendance_date'],
            'reason' => $validated['reason'],
            'attachment_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('staff.mc.index')->with('success', 'MC submitted successfully and is pending review.');
    }

    public function approve(MedicalCertificate $medicalCertificate)
    {
        $medicalCertificate->status = 'approved';
        $medicalCertificate->admin_remark = $medicalCertificate->admin_remark ?? 'Approved by administrator';
        $medicalCertificate->save();

        // Record or update attendance entry to reflect approved Medical Leave
        \App\Models\Attendance::updateOrCreate(
            [
                'employee_id' => $medicalCertificate->employee_id,
                'attendance_date' => $medicalCertificate->attendance_date,
            ],
            [
                'status' => 'On Leave (MC)',
                'verification_method' => 'Medical Certificate',
            ]
        );

        return redirect()->route('admin.mc.index')->with('success', 'Medical certificate approved and attendance status updated.');
    }

    public function reject(Request $request, MedicalCertificate $medicalCertificate)
    {
        $request->validate([
            'admin_remark' => ['nullable', 'string', 'max:500'],
        ]);

        $medicalCertificate->status = 'rejected';
        $medicalCertificate->admin_remark = $request->input('admin_remark', 'Rejected by administrator');
        $medicalCertificate->save();

        return redirect()->route('admin.mc.index')->with('success', 'Medical certificate rejected.');
    }

    public function download(MedicalCertificate $medicalCertificate): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($medicalCertificate->attachment_path), 404, 'MC file not found.');

        return Storage::disk('public')->response($medicalCertificate->attachment_path);
    }

    public function staffDownload(MedicalCertificate $medicalCertificate): StreamedResponse
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->orWhere('name', $user->name)
            ->first();

        abort_unless($employee && (int) $medicalCertificate->employee_id === (int) $employee->id, 403, 'Unauthorized access to this document.');
        abort_unless(Storage::disk('public')->exists($medicalCertificate->attachment_path), 404, 'MC file not found.');

        return Storage::disk('public')->response($medicalCertificate->attachment_path);
    }
}
