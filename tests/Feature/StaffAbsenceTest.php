<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAbsenceTest extends TestCase
{
    use RefreshDatabase;

    private function createStaffWithEmployee(string $name = 'John Doe', string $empId = 'EMP-001'): array
    {
        $email = strtolower(str_replace(' ', '.', $name)) . '@ttdental.com';

        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'role' => 'staff',
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => $empId,
            'name' => $name,
            'email' => $email,
            'position' => 'Dental Technician',
            'status' => 'active',
        ]);

        return [$user, $employee];
    }

    public function test_staff_can_view_absence_page_when_empty(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        $response = $this->actingAs($user)->get(route('staff.absences.index'));

        $response->assertStatus(200);
        $response->assertSee('Report Absence');
        $response->assertSee('Submit Absence Notice');
        $response->assertSee('No absence records submitted yet');
        $response->assertSee('EMP-001 — John Doe');
    }

    public function test_staff_can_submit_absence_report_for_themselves(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        $response = $this->actingAs($user)->post(route('staff.absences.store'), [
            'absence_date' => '2026-10-10',
            'reason' => 'Emergency Leave',
            'remarks' => 'Urgent domestic pipeline emergency',
        ]);

        $response->assertRedirect(route('staff.absences.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('absences', [
            'employee_id' => $employee->id,
            'absence_date' => '2026-10-10 00:00:00',
            'reason' => 'Emergency Leave',
            'remarks' => 'Urgent domestic pipeline emergency',
        ]);
    }

    public function test_staff_submission_always_uses_logged_in_staff_employee_id(): void
    {
        [$user1, $employee1] = $this->createStaffWithEmployee('Alice Wong', 'EMP-010');
        [$user2, $employee2] = $this->createStaffWithEmployee('Bob Tan', 'EMP-020');

        // User 1 tries to send employee_id of Employee 2
        $response = $this->actingAs($user1)->post(route('staff.absences.store'), [
            'employee_id' => $employee2->id,
            'absence_date' => '2026-10-15',
            'reason' => 'Personal Matter',
            'remarks' => 'Personal errands',
        ]);

        $response->assertRedirect(route('staff.absences.index'));

        // The record MUST belong to Employee 1, NOT Employee 2
        $this->assertDatabaseHas('absences', [
            'employee_id' => $employee1->id,
            'absence_date' => '2026-10-15 00:00:00',
            'reason' => 'Personal Matter',
        ]);

        $this->assertDatabaseMissing('absences', [
            'employee_id' => $employee2->id,
            'absence_date' => '2026-10-15 00:00:00',
        ]);
    }

    public function test_staff_can_view_their_own_submitted_absence_records(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Absence::create([
            'employee_id' => $employee->id,
            'absence_date' => '2026-10-05',
            'reason' => 'Sick Leave (Uncertified)',
            'remarks' => 'Mild cold and fever symptoms',
        ]);

        $response = $this->actingAs($user)->get(route('staff.absences.index'));

        $response->assertStatus(200);
        $response->assertSee('Sick Leave (Uncertified)');
        $response->assertSee('Mild cold and fever symptoms');
        $response->assertSee('05 October 2026');
    }

    public function test_staff_cannot_delete_another_employees_absence_record(): void
    {
        [$user1, $employee1] = $this->createStaffWithEmployee('Staff One', 'EMP-001');
        [$user2, $employee2] = $this->createStaffWithEmployee('Staff Two', 'EMP-002');

        $record2 = Absence::create([
            'employee_id' => $employee2->id,
            'absence_date' => '2026-10-06',
            'reason' => 'Annual Leave',
        ]);

        // User 1 tries to delete record belonging to Employee 2
        $response = $this->actingAs($user1)->delete(route('staff.absences.destroy', $record2));

        $response->assertStatus(403);
        $this->assertDatabaseHas('absences', ['id' => $record2->id]);
    }

    public function test_staff_can_delete_their_own_absence_record(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        $record = Absence::create([
            'employee_id' => $employee->id,
            'absence_date' => '2026-10-07',
            'reason' => 'Family Matter',
        ]);

        $response = $this->actingAs($user)->delete(route('staff.absences.destroy', $record));

        $response->assertRedirect();
        $this->assertDatabaseMissing('absences', ['id' => $record->id]);
    }

    public function test_admin_can_view_staff_submitted_absence_records(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee('David Miller', 'EMP-099');

        Absence::create([
            'employee_id' => $employee->id,
            'absence_date' => '2026-10-04',
            'reason' => 'Medical Appointment',
            'remarks' => 'Scheduled dental scan follow-up',
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin Boss',
            'email' => 'admin@ttdental.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('absences.index'));

        $response->assertStatus(200);
        $response->assertSee('Absence Management');
        $response->assertSee('David Miller');
        $response->assertSee('Medical Appointment');
        $response->assertSee('Scheduled dental scan follow-up');
    }
}
