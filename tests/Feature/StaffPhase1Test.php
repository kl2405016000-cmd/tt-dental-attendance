<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\MedicalCertificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffPhase1Test extends TestCase
{
    use RefreshDatabase;

    private function createStaffWithEmployee(): array
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john.doe@ttdental.com',
            'role' => 'staff',
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-001',
            'name' => 'John Doe',
            'email' => 'john.doe@ttdental.com',
            'position' => 'Dental Technician',
            'status' => 'active',
        ]);

        return [$user, $employee];
    }

    public function test_staff_attendance_history_renders_correctly_when_empty(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        $response = $this->actingAs($user)->get(route('staff.history'));

        $response->assertStatus(200);
        $response->assertSee('My Attendance History');
        $response->assertSee('No attendance logs found');
        $response->assertSee('EMP-001 — John Doe');
    }

    public function test_staff_attendance_history_renders_records_and_calculates_stats(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => now()->toDateString(),
            'clock_in' => '08:30:00',
            'clock_out' => '17:30:00',
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
            'break_duration_minutes' => 60,
            'total_work_minutes' => 480,
            'status' => 'Present',
            'verification_method' => 'Face Recognition',
        ]);

        $response = $this->actingAs($user)->get(route('staff.history'));

        $response->assertStatus(200);
        $response->assertSee('Daily Attendance Records');
        $response->assertSee('Present');
        $response->assertSee('08:30 AM');
        $response->assertSee('05:30 PM');
        $response->assertSee('Biometric');
    }

    public function test_staff_attendance_history_filtering(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-09-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => 'Present',
            'verification_method' => 'Manual',
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-09-15',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => 'Late',
            'verification_method' => 'Manual',
        ]);

        // Filter for specific date range
        $resRange = $this->actingAs($user)->get(route('staff.history', [
            'date_from' => '2026-09-10',
            'date_to' => '2026-09-20',
        ]));
        $resRange->assertStatus(200);
        $resRange->assertSee('15 Sep 2026');
        $resRange->assertDontSee('01 Sep 2026');

        // Filter for status
        $resStatus = $this->actingAs($user)->get(route('staff.history', [
            'status' => 'Present',
        ]));
        $resStatus->assertStatus(200);
        $resStatus->assertSee('01 Sep 2026');
        $resStatus->assertDontSee('15 Sep 2026');
    }

    public function test_staff_mc_page_renders_correctly_when_empty(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        $response = $this->actingAs($user)->get(route('staff.mc.index'));

        $response->assertStatus(200);
        $response->assertSee('Medical Certificates');
        $response->assertSee('Submit Medical Certificate');
        $response->assertSee('No medical certificates submitted yet');
    }

    public function test_staff_can_submit_medical_certificate_with_attachment(): void
    {
        Storage::fake('public');
        [$user, $employee] = $this->createStaffWithEmployee();

        $file = UploadedFile::fake()->create('medical_cert.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('staff.mc.store'), [
            'attendance_date' => '2026-10-02',
            'reason' => 'Severe acute viral infection and high fever',
            'attachment' => $file,
        ]);

        $response->assertRedirect(route('staff.mc.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('medical_certificates', [
            'employee_id' => $employee->id,
            'attendance_date' => '2026-10-02 00:00:00',
            'reason' => 'Severe acute viral infection and high fever',
            'status' => 'pending',
        ]);

        $mc = MedicalCertificate::first();
        Storage::disk('public')->assertExists($mc->attachment_path);
    }

    public function test_staff_mc_page_displays_mc_records_with_status_and_remarks(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        MedicalCertificate::create([
            'employee_id' => $employee->id,
            'attendance_date' => '2026-10-01',
            'reason' => 'Migraine attack',
            'attachment_path' => 'mc-uploads/test.pdf',
            'status' => 'approved',
            'admin_remark' => 'Verified by HR Administrator',
        ]);

        $response = $this->actingAs($user)->get(route('staff.mc.index'));

        $response->assertStatus(200);
        $response->assertSee('Approved');
        $response->assertSee('Migraine attack');
        $response->assertSee('Verified by HR Administrator');
    }

    public function test_staff_cannot_download_another_employees_mc(): void
    {
        Storage::fake('public');
        [$user1, $employee1] = $this->createStaffWithEmployee();

        $user2 = User::factory()->create(['role' => 'staff']);
        $employee2 = Employee::create([
            'user_id' => $user2->id,
            'employee_id' => 'EMP-002',
            'name' => 'Jane Smith',
            'email' => 'jane@ttdental.com',
            'status' => 'active',
        ]);

        $fakePath = 'mc-uploads/secret.pdf';
        Storage::disk('public')->put($fakePath, 'PDF content');

        $mc2 = MedicalCertificate::create([
            'employee_id' => $employee2->id,
            'attendance_date' => '2026-10-01',
            'reason' => 'Jane private illness',
            'attachment_path' => $fakePath,
            'status' => 'pending',
        ]);

        // User 1 tries to download User 2's MC
        $response = $this->actingAs($user1)->get(route('staff.mc.download', $mc2));
        $response->assertStatus(403);
    }
}
