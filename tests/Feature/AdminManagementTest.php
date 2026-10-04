<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\MedicalCertificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Admin Boss',
            'email' => 'admin@ttdental.com',
            'role' => 'admin',
        ]);
    }

    public function test_admin_dashboard_renders_stats_and_recent_attendance(): void
    {
        $admin = $this->createAdmin();

        $employee = Employee::create([
            'employee_id' => 'EMP-001',
            'name' => 'Omar Dental',
            'position' => 'Dental Technician',
            'status' => true,
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => now()->toDateString(),
            'clock_in' => '08:45:00',
            'clock_out' => '17:30:00',
            'status' => 'Present',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Total Staff');
        $response->assertSee('Omar Dental');
    }

    public function test_admin_employee_management_crud(): void
    {
        $admin = $this->createAdmin();

        // 1. Index
        $response = $this->actingAs($admin)->get(route('employees.index'));
        $response->assertStatus(200);
        $response->assertSee('Employee Directory');

        // 2. Create form
        $responseCreate = $this->actingAs($admin)->get(route('employees.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Register New Employee');

        // 3. Store employee
        $responseStore = $this->actingAs($admin)->post(route('employees.store'), [
            'employee_id' => 'EMP-100',
            'name' => 'Ahmad Ibrahim',
            'position' => 'Dental Assistant',
            'department' => 'Prosthodontics',
            'email' => 'ahmad@ttdental.com',
            'phone' => '+60123456789',
            'employment_status' => 'Full-Time',
        ]);
        $responseStore->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', ['employee_id' => 'EMP-100', 'name' => 'Ahmad Ibrahim']);

        $emp = Employee::where('employee_id', 'EMP-100')->first();

        // 4. Edit form
        $responseEdit = $this->actingAs($admin)->get(route('employees.edit', $emp));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('Edit Employee: Ahmad Ibrahim');

        // 5. Update
        $responseUpdate = $this->actingAs($admin)->put(route('employees.update', $emp), [
            'employee_id' => 'EMP-100',
            'name' => 'Ahmad Ibrahim Updated',
            'position' => 'Senior Dental Assistant',
            'employment_status' => 'Full-Time',
            'status' => 1,
        ]);
        $responseUpdate->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', ['name' => 'Ahmad Ibrahim Updated']);

        // 6. Toggle status
        $this->actingAs($admin)->patch(route('employees.toggle-status', $emp));
        $this->assertDatabaseHas('employees', ['id' => $emp->id, 'status' => false]);
    }

    public function test_admin_manual_attendance(): void
    {
        $admin = $this->createAdmin();

        $employee = Employee::create([
            'employee_id' => 'EMP-002',
            'name' => 'Sara Dental',
            'status' => true,
        ]);

        // Clock In
        $resIn = $this->actingAs($admin)->post(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
        ]);
        $resIn->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status' => 'Present',
        ]);

        // Clock Out
        $resOut = $this->actingAs($admin)->post(route('attendance.clockOut'), [
            'employee_id' => $employee->id,
        ]);
        $resOut->assertRedirect();
        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNotNull($att->clock_out);
    }

    public function test_admin_mc_review_approve_and_reject(): void
    {
        $admin = $this->createAdmin();

        $employee = Employee::create([
            'employee_id' => 'EMP-003',
            'name' => 'Bob Technician',
            'status' => true,
        ]);

        $mc1 = MedicalCertificate::create([
            'employee_id' => $employee->id,
            'attendance_date' => now()->toDateString(),
            'reason' => 'Acute tooth infection',
            'attachment_path' => 'mc-uploads/cert1.pdf',
            'status' => 'pending',
        ]);

        $mc2 = MedicalCertificate::create([
            'employee_id' => $employee->id,
            'attendance_date' => now()->subDay()->toDateString(),
            'reason' => 'Back sprain',
            'attachment_path' => 'mc-uploads/cert2.pdf',
            'status' => 'pending',
        ]);

        // View MC list
        $response = $this->actingAs($admin)->get(route('admin.mc.index'));
        $response->assertStatus(200);
        $response->assertSee('Medical Certificate Management');
        $response->assertSee('Acute tooth infection');

        // Approve MC1
        $resApprove = $this->actingAs($admin)->post(route('admin.mc.approve', $mc1));
        $resApprove->assertRedirect(route('admin.mc.index'));
        $this->assertDatabaseHas('medical_certificates', ['id' => $mc1->id, 'status' => 'approved']);
        $this->assertDatabaseHas('attendances', ['employee_id' => $employee->id, 'status' => 'On Leave (MC)']);

        // Reject MC2
        $resReject = $this->actingAs($admin)->post(route('admin.mc.reject', $mc2), [
            'admin_remark' => 'Invalid clinic receipt',
        ]);
        $resReject->assertRedirect(route('admin.mc.index'));
        $this->assertDatabaseHas('medical_certificates', ['id' => $mc2->id, 'status' => 'rejected', 'admin_remark' => 'Invalid clinic receipt']);
    }

    public function test_admin_absence_management(): void
    {
        $admin = $this->createAdmin();

        $employee = Employee::create([
            'employee_id' => 'EMP-004',
            'name' => 'David Lee',
            'status' => true,
        ]);

        // View absences
        $response = $this->actingAs($admin)->get(route('absences.index'));
        $response->assertStatus(200);
        $response->assertSee('Absence Management');
        $response->assertSee('David Lee');

        // Store absence reason
        $resStore = $this->actingAs($admin)->post(route('absences.store'), [
            'employee_id' => $employee->id,
            'absence_date' => now()->toDateString(),
            'reason' => 'Emergency Leave',
            'remarks' => 'Family medical emergency',
        ]);
        $resStore->assertRedirect();
        $this->assertDatabaseHas('absences', [
            'employee_id' => $employee->id,
            'reason' => 'Emergency Leave',
        ]);

        $absence = Absence::where('employee_id', $employee->id)->first();

        // Destroy absence
        $resDelete = $this->actingAs($admin)->delete(route('absences.destroy', $absence));
        $resDelete->assertRedirect();
        $this->assertDatabaseMissing('absences', ['id' => $absence->id]);
    }

    public function test_admin_reports_and_settings_pages(): void
    {
        $admin = $this->createAdmin();

        // Reports
        $resReports = $this->actingAs($admin)->get(route('reports.attendance'));
        $resReports->assertStatus(200);
        $resReports->assertSee('Attendance Reports');

        // Export
        $resExport = $this->actingAs($admin)->get(route('reports.attendance.export'));
        $resExport->assertStatus(200);

        // Settings
        $resSettings = $this->actingAs($admin)->get(route('settings.index'));
        $resSettings->assertStatus(200);
        $resSettings->assertSee('System Settings');
        $resSettings->assertSee('Face Recognition Settings');

        // Face registration page
        $resFace = $this->actingAs($admin)->get(route('face-attendance'));
        $resFace->assertStatus(200);
        $resFace->assertSee('Register Employee Biometric Face Profile');
    }
}
