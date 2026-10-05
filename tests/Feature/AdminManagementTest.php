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

    public function test_admin_employee_biodata_profile_view(): void
    {
        $admin = $this->createAdmin();

        $employee = Employee::create([
            'employee_id' => 'EMP-777',
            'name' => 'Dr. Aiman',
            'ic_passport_number' => '900101-14-1234',
            'date_of_birth' => '1990-01-01',
            'gender' => 'Male',
            'position' => 'Dental Technician',
            'department' => 'Crown & Bridge',
            'employment_status' => 'Full-Time',
            'date_joined' => '2023-01-15',
            'email' => 'aiman@ttdental.com',
            'phone' => '+601122334455',
            'address' => 'No 12, Jalan Damai, KL',
            'emergency_contact_name' => 'Nurul Huda',
            'emergency_contact_relationship' => 'Spouse',
            'emergency_contact_phone' => '+60198877665',
            'emergency_contact_address' => 'Same as home',
            'status' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('employees.show', $employee));
        $response->assertStatus(200);
        $response->assertSee('Staff Biodata Profile');
        $response->assertSee('1. Personal Information');
        $response->assertSee('2. Employment Information');
        $response->assertSee('3. Emergency Contact');
        $response->assertSee('4. System Information');
        $response->assertSee('EMP-777');
        $response->assertSee('900101-14-1234');
        $response->assertSee('Nurul Huda');
        $response->assertSee('Crown & Bridge');
    }

    public function test_admin_can_save_and_update_full_biodata_fields(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('employees.store'), [
            'employee_id' => 'EMP-888',
            'name' => 'Siti Aminah',
            'ic_passport_number' => '980512-10-5432',
            'date_of_birth' => '1998-05-12',
            'gender' => 'Female',
            'position' => 'CAD/CAM Technician',
            'department' => 'CAD/CAM',
            'employment_status' => 'Full-Time',
            'date_joined' => '2024-03-01',
            'email' => 'siti@ttdental.com',
            'phone' => '+60171234567',
            'address' => 'Apartment Orkid, Cheras',
            'emergency_contact_name' => 'Mohd Amin',
            'emergency_contact_relationship' => 'Father',
            'emergency_contact_phone' => '+60129998877',
            'emergency_contact_address' => 'Bangi, Selangor',
            'status' => 1,
        ]);

        $response->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', [
            'employee_id' => 'EMP-888',
            'ic_passport_number' => '980512-10-5432',
            'gender' => 'Female',
            'emergency_contact_name' => 'Mohd Amin',
        ]);

        $emp = Employee::where('employee_id', 'EMP-888')->first();

        // Update with custom position and department via Other option
        $responseUpdate = $this->actingAs($admin)->put(route('employees.update', $emp), [
            'employee_id' => 'EMP-888',
            'name' => 'Siti Aminah Updated',
            'ic_passport_number' => '980512-10-5432',
            'date_of_birth' => '1998-05-12',
            'gender' => 'Female',
            'position' => 'Other',
            'custom_position' => 'Lead Biomaterial Specialist',
            'department' => 'Other',
            'custom_department' => 'R&D Innovation',
            'employment_status' => 'Contract',
            'date_joined' => '2024-03-01',
            'email' => 'siti.updated@ttdental.com',
            'phone' => '+60171234567',
            'address' => 'Updated Address',
            'emergency_contact_name' => 'Mohd Amin',
            'emergency_contact_relationship' => 'Father',
            'emergency_contact_phone' => '+60129998877',
            'status' => 1,
        ]);

        $responseUpdate->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', [
            'employee_id' => 'EMP-888',
            'position' => 'Lead Biomaterial Specialist',
            'department' => 'R&D Innovation',
            'employment_status' => 'Contract',
        ]);
    }

    public function test_existing_legacy_employee_record_retains_data_and_profile_view(): void
    {
        $admin = $this->createAdmin();

        $staffUser = User::factory()->create([
            'name' => 'HARTINI',
            'email' => 'tinih9554@gmail.com',
            'role' => 'staff',
        ]);

        $employee = Employee::create([
            'employee_id' => 'EMP005',
            'name' => 'HARTINI',
            'position' => 'DENTAL TECHNICIAN',
            'department' => 'TECHNICIAN',
            'employment_status' => 'Full-Time',
            'email' => 'tinih9554@gmail.com',
            'phone' => '0136146379',
            'user_id' => $staffUser->id,
            'status' => true,
        ]);

        // 1. Profile / details view
        $responseShow = $this->actingAs($admin)->get(route('employees.show', $employee));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Staff Biodata Profile');
        $responseShow->assertSee('EMP005');
        $responseShow->assertSee('HARTINI');
        $responseShow->assertSee('DENTAL TECHNICIAN');
        $responseShow->assertSee('TECHNICIAN');
        $responseShow->assertSee('tinih9554@gmail.com');
        $responseShow->assertSee('Staff');
        // Ensure password is NEVER exposed
        $responseShow->assertDontSee($staffUser->password);

        // 2. Edit view contains legacy values in dropdowns
        $responseEdit = $this->actingAs($admin)->get(route('employees.edit', $employee));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('Edit Employee: HARTINI');
        $responseEdit->assertSee('DENTAL TECHNICIAN');
        $responseEdit->assertSee('TECHNICIAN');
        $responseEdit->assertDontSee($staffUser->password);

        // 3. Updating other fields preserves legacy values
        $responseUpdate = $this->actingAs($admin)->put(route('employees.update', $employee), [
            'employee_id' => 'EMP005',
            'name' => 'HARTINI BINTI ISMAIL',
            'position' => 'DENTAL TECHNICIAN',
            'department' => 'TECHNICIAN',
            'employment_status' => 'Full-Time',
            'ic_passport_number' => '950505-14-1234',
            'gender' => 'Female',
            'emergency_contact_name' => 'Ismail',
            'emergency_contact_relationship' => 'Father',
            'emergency_contact_phone' => '0123456789',
            'status' => 1,
        ]);

        $responseUpdate->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', [
            'employee_id' => 'EMP005',
            'name' => 'HARTINI BINTI ISMAIL',
            'position' => 'DENTAL TECHNICIAN',
            'department' => 'TECHNICIAN',
            'ic_passport_number' => '950505-14-1234',
            'gender' => 'Female',
            'user_id' => $staffUser->id,
        ]);
    }
}
