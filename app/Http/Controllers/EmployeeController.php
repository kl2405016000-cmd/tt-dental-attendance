<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get();
        $attendances = Attendance::with('employee')
            ->orderByDesc('attendance_date')
            ->orderByDesc('clock_in')
            ->get();

        return view('employees.index', compact('employees', 'attendances'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:employees,employee_id',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $validated['status'] = $request->boolean('status', true);

        if ($request->boolean('create_account') || $request->filled('password')) {
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
                'role' => 'staff',
            ]);

            $validated['user_id'] = $user->id;
        }

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:employees,employee_id,' . $employee->id,
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
                Storage::disk('public')->delete($employee->profile_photo);
            }

            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $validated['status'] = $request->has('status') ? $request->boolean('status') : $employee->status;

        if ($request->boolean('create_account') && ! $employee->user_id) {
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
                'role' => 'staff',
            ]);

            $validated['user_id'] = $user->id;
        } elseif ($employee->user && $request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:6',
            ]);

            $employee->user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
            ]);
        }

        if ($employee->user && isset($validated['email']) && $validated['email'] !== $employee->user->email) {
            $employee->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);
        }

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee details updated successfully.');
    }

    public function toggleStatus(Employee $employee)
    {
        $employee->status = ! $employee->status;
        $employee->save();

        $statusText = $employee->status ? 'activated' : 'deactivated';

        return redirect()->route('employees.index')->with('success', "Employee {$employee->name} has been {$statusText}.");
    }

    public function destroy(Employee $employee)
    {
        $employeeName = $employee->name;

        if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', "Employee {$employeeName} deleted successfully.");
    }
}
