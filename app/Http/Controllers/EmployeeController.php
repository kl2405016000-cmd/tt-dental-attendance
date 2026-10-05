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
        $employees = Employee::with(['faceRegistration', 'user'])->orderBy('name')->get();
        $attendances = Attendance::with('employee')
            ->orderByDesc('attendance_date')
            ->orderByDesc('clock_in')
            ->get();

        return view('employees.index', compact('employees', 'attendances'));
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'faceRegistration', 'attendances' => function ($q) {
            $q->orderByDesc('attendance_date')->orderByDesc('clock_in')->limit(10);
        }]);

        return view('employees.show', compact('employee'));
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
            'ic_passport_number' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'custom_position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'custom_department' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|max:255',
            'date_joined' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_address' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if (($validated['position'] ?? null) === 'Other' && !empty($validated['custom_position'])) {
            $validated['position'] = $validated['custom_position'];
        }
        unset($validated['custom_position']);

        if (($validated['department'] ?? null) === 'Other' && !empty($validated['custom_department'])) {
            $validated['department'] = $validated['custom_department'];
        }
        unset($validated['custom_department']);

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
            'ic_passport_number' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'custom_position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'custom_department' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|max:255',
            'date_joined' => 'nullable|date',
            'email' => [
                'nullable',
                'email',
                'max:255',
                $employee->user_id ? \Illuminate\Validation\Rule::unique('users', 'email')->ignore($employee->user_id) : '',
            ],
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_address' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if (($validated['position'] ?? null) === 'Other' && !empty($validated['custom_position'])) {
            $validated['position'] = $validated['custom_position'];
        }
        unset($validated['custom_position']);

        if (($validated['department'] ?? null) === 'Other' && !empty($validated['custom_department'])) {
            $validated['department'] = $validated['custom_department'];
        }
        unset($validated['custom_department']);

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

        if ($employee->user) {
            $userUpdates = ['name' => $validated['name']];
            if (!empty($validated['email']) && $validated['email'] !== $employee->user->email) {
                $userUpdates['email'] = $validated['email'];
            }
            $employee->user->update($userUpdates);
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
