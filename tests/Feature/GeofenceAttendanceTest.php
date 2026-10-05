<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeofenceAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private float $workplaceLat = 3.139000;
    private float $workplaceLng = 101.686900;
    private float $allowedRadius = 100.0; // 100 meters

    protected function setUp(): void
    {
        parent::setUp();

        // Configure workplace geofence in settings
        Setting::setVal('workplace_lat', (string) $this->workplaceLat);
        Setting::setVal('workplace_lng', (string) $this->workplaceLng);
        Setting::setVal('workplace_radius', (string) $this->allowedRadius);
    }

    private function createStaffWithEmployee(): array
    {
        $user = User::factory()->create([
            'name' => 'Alice Tan',
            'email' => 'alice@ttdental.com',
            'role' => 'staff',
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-010',
            'name' => 'Alice Tan',
            'email' => 'alice@ttdental.com',
            'position' => 'Dental Technician',
            'status' => true,
        ]);

        return [$user, $employee];
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Admin Boss',
            'email' => 'admin@ttdental.com',
            'role' => 'admin',
        ]);
    }

    // =========================================================================
    // 1. CLOCK IN GEOFENCE TESTS
    // =========================================================================

    public function test_clock_in_blocked_when_outside_workplace_radius(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Coordinates ~1.1km away from workplace
        $outsideLat = 3.149000;
        $outsideLng = 101.686900;

        $response = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => $outsideLat,
            'lng' => $outsideLng,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Outside Allowed Location', $response->json('message'));

        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
        ]);
    }

    public function test_clock_in_blocked_when_gps_permission_denied_or_unavailable(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Missing GPS coordinates (denied or unavailable)
        $response = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'GPS location permission is required. Attendance cannot be recorded without verified workplace location.',
        ]);

        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
        ]);
    }

    public function test_clock_in_blocked_when_gps_coordinates_are_null_or_empty(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        $response = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => null,
            'lng' => '',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'GPS location permission is required. Attendance cannot be recorded without verified workplace location.',
        ]);
    }

    public function test_clock_in_allowed_when_inside_workplace_radius(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Coordinates ~2 meters away (within 100m)
        $insideLat = 3.139015;
        $insideLng = 101.686915;

        $response = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => $insideLat,
            'lng' => $insideLng,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status' => 'Present',
        ]);
        $this->assertTrue(Attendance::where('employee_id', $employee->id)->whereDate('attendance_date', Carbon::today())->exists());
    }

    // =========================================================================
    // 2. START BREAK GEOFENCE TESTS
    // =========================================================================

    public function test_start_break_blocked_when_outside_workplace_radius(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Clocked in first
        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'status' => 'Present',
        ]);

        // Attempt break start outside allowed location
        $outsideLat = 3.150000;
        $outsideLng = 101.686900;

        $response = $this->actingAs($user)->postJson(route('attendance.startBreak'), [
            'employee_id' => $employee->id,
            'lat' => $outsideLat,
            'lng' => $outsideLng,
        ]);

        $response->assertStatus(403);
        $this->assertStringContainsString('Outside Allowed Location', $response->json('message'));

        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNull($att->break_start);
        $this->assertEquals('Present', $att->status);
    }

    public function test_start_break_blocked_when_gps_missing(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'status' => 'Present',
        ]);

        $response = $this->actingAs($user)->postJson(route('attendance.startBreak'), [
            'employee_id' => $employee->id,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'GPS location permission is required. Attendance cannot be recorded without verified workplace location.',
        ]);

        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNull($att->break_start);
    }

    public function test_start_break_allowed_when_inside_workplace_radius(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'status' => 'Present',
        ]);

        $insideLat = 3.139010;
        $insideLng = 101.686910;

        $response = $this->actingAs($user)->postJson(route('attendance.startBreak'), [
            'employee_id' => $employee->id,
            'lat' => $insideLat,
            'lng' => $insideLng,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'on_break',
        ]);

        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNotNull($att->break_start);
        $this->assertEquals('On Break', $att->status);
    }

    // =========================================================================
    // 3. END BREAK GEOFENCE TESTS
    // =========================================================================

    public function test_end_break_blocked_when_outside_workplace_radius(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'break_start' => '12:00:00',
            'status' => 'On Break',
        ]);

        $outsideLat = 3.160000;
        $outsideLng = 101.686900;

        $response = $this->actingAs($user)->postJson(route('attendance.endBreak'), [
            'employee_id' => $employee->id,
            'lat' => $outsideLat,
            'lng' => $outsideLng,
        ]);

        $response->assertStatus(403);
        $this->assertStringContainsString('Outside Allowed Location', $response->json('message'));

        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNull($att->break_end);
        $this->assertEquals('On Break', $att->status);
    }

    public function test_end_break_blocked_when_gps_missing(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'break_start' => '12:00:00',
            'status' => 'On Break',
        ]);

        $response = $this->actingAs($user)->postJson(route('attendance.endBreak'), [
            'employee_id' => $employee->id,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'GPS location permission is required. Attendance cannot be recorded without verified workplace location.',
        ]);

        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNull($att->break_end);
    }

    public function test_end_break_allowed_when_inside_workplace_radius(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'break_start' => '12:00:00',
            'status' => 'On Break',
        ]);

        $insideLat = 3.139005;
        $insideLng = 101.686905;

        $response = $this->actingAs($user)->postJson(route('attendance.endBreak'), [
            'employee_id' => $employee->id,
            'lat' => $insideLat,
            'lng' => $insideLng,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'clocked_in',
        ]);

        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNotNull($att->break_end);
        $this->assertEquals('Present', $att->status);
    }

    // =========================================================================
    // 4. CLOCK OUT GEOFENCE TESTS
    // =========================================================================

    public function test_clock_out_blocked_when_outside_workplace_radius(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'status' => 'Present',
        ]);

        $outsideLat = 3.155000;
        $outsideLng = 101.686900;

        $response = $this->actingAs($user)->postJson(route('attendance.clockOut'), [
            'employee_id' => $employee->id,
            'lat' => $outsideLat,
            'lng' => $outsideLng,
        ]);

        $response->assertStatus(403);
        $this->assertStringContainsString('Outside Allowed Location', $response->json('message'));

        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNull($att->clock_out);
    }

    public function test_clock_out_blocked_when_gps_missing(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'status' => 'Present',
        ]);

        $response = $this->actingAs($user)->postJson(route('attendance.clockOut'), [
            'employee_id' => $employee->id,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'GPS location permission is required. Attendance cannot be recorded without verified workplace location.',
        ]);

        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNull($att->clock_out);
    }

    public function test_clock_out_allowed_when_inside_workplace_radius(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'status' => 'Present',
        ]);

        $insideLat = 3.139020;
        $insideLng = 101.686910;

        $response = $this->actingAs($user)->postJson(route('attendance.clockOut'), [
            'employee_id' => $employee->id,
            'lat' => $insideLat,
            'lng' => $insideLng,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'completed',
        ]);

        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNotNull($att->clock_out);
    }

    // =========================================================================
    // 5. ADMIN MANUAL OVERRIDE (DESKTOP) EXEMPTION TEST
    // =========================================================================

    public function test_admin_manual_attendance_override_is_not_blocked_by_missing_gps(): void
    {
        $admin = $this->createAdmin();

        $employee = Employee::create([
            'employee_id' => 'EMP-099',
            'name' => 'Manual Worker',
            'status' => true,
        ]);

        // Admin manual clock in from admin portal (HTML form post)
        $resIn = $this->actingAs($admin)->post(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
        ]);
        $resIn->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status' => 'Present',
        ]);
        $this->assertTrue(Attendance::where('employee_id', $employee->id)->whereDate('attendance_date', Carbon::today())->exists());

        // Admin manual clock out from admin portal
        $resOut = $this->actingAs($admin)->post(route('attendance.clockOut'), [
            'employee_id' => $employee->id,
        ]);
        $resOut->assertRedirect();
        $att = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNotNull($att->clock_out);
    }

    // =========================================================================
    // 6. FAIL-CLOSED: UNCONFIGURED WORKPLACE GEOFENCE TESTS
    // =========================================================================

    public function test_attendance_blocked_when_workplace_geofence_not_configured(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Clear or set workplace coordinates to 0 (unconfigured)
        Setting::setVal('workplace_lat', '0');
        Setting::setVal('workplace_lng', '0');
        Setting::setVal('workplace_radius', '0');

        $response = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => 3.1390,
            'lng' => 101.6869,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Workplace GPS geofence is not configured. Please contact the administrator to set the laboratory coordinates.',
        ]);

        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $employee->id,
        ]);
    }

    public function test_attendance_blocked_when_gps_coordinates_are_out_of_range(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Latitude > 90
        $responseLat = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => 95.1234,
            'lng' => 101.6869,
        ]);
        $responseLat->assertStatus(403);
        $this->assertStringContainsString('Invalid GPS coordinates provided', $responseLat->json('message'));

        // Longitude > 180
        $responseLng = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => 3.1390,
            'lng' => 195.1234,
        ]);
        $responseLng->assertStatus(403);
        $this->assertStringContainsString('Invalid GPS coordinates provided', $responseLng->json('message'));
    }

    // =========================================================================
    // 7. MULTI-LOCATION GEOFENCE SCENARIOS (A, B, C, D, E, F)
    // =========================================================================

    public function test_scenario_a_user_inside_workplace_attendance_allowed(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Location 1: Workplace (Shop Apartment, Jalan Orkid 4, Cheras: 3.0783092, 101.7664661)
        Setting::setVal('location_1_lat', '3.0783092');
        Setting::setVal('location_1_lng', '101.7664661');
        Setting::setVal('location_1_radius', '500');

        $response = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => 3.0783100,
            'lng' => 101.7664670,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status' => 'Present',
        ]);
    }

    public function test_scenario_b_user_inside_home_demo_attendance_allowed(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Location 2: Home / Demo (The Sky Residensi, Shamelin: 3.1247527, 101.7374223)
        Setting::setVal('location_2_lat', '3.1247527');
        Setting::setVal('location_2_lng', '101.7374223');
        Setting::setVal('location_2_radius', '500');

        $response = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => 3.1247500,
            'lng' => 101.7374200,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status' => 'Present',
        ]);
    }

    public function test_scenario_c_user_inside_college_demo_attendance_allowed(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Location 3: College / Demo (UPTM: 3.1276441, 101.7373364)
        Setting::setVal('location_3_lat', '3.1276441');
        Setting::setVal('location_3_lng', '101.7373364');
        Setting::setVal('location_3_radius', '500');

        $response = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => 3.1276400,
            'lng' => 101.7373300,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status' => 'Present',
        ]);
    }

    public function test_scenario_d_user_outside_all_three_attendance_blocked(): void
    {
        [$user, $employee] = $this->createStaffWithEmployee();

        // Ensure all three locations are active with 500m radius
        Setting::setVal('location_1_lat', '3.0783092');
        Setting::setVal('location_1_lng', '101.7664661');
        Setting::setVal('location_1_radius', '500');

        Setting::setVal('location_2_lat', '3.1247527');
        Setting::setVal('location_2_lng', '101.7374223');
        Setting::setVal('location_2_radius', '500');

        Setting::setVal('location_3_lat', '3.1276441');
        Setting::setVal('location_3_lng', '101.7373364');
        Setting::setVal('location_3_radius', '500');

        // Far location (~10km away in Petaling Jaya)
        $response = $this->actingAs($user)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employee->id,
            'lat' => 3.100000,
            'lng' => 101.600000,
        ]);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('Outside Allowed Location', $response->json('message'));
        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $employee->id,
        ]);
    }

    public function test_scenario_e_staff_cannot_access_another_employee_attendance(): void
    {
        [$userA, $employeeA] = $this->createStaffWithEmployee();

        $userB = User::factory()->create([
            'name' => 'Bob Lee',
            'email' => 'bob@ttdental.com',
            'role' => 'staff',
        ]);
        $employeeB = Employee::create([
            'user_id' => $userB->id,
            'employee_id' => 'EMP-020',
            'name' => 'Bob Lee',
            'email' => 'bob@ttdental.com',
            'position' => 'Dental Assistant',
            'status' => true,
        ]);

        // User A attempts to clock in for Employee B
        $response = $this->actingAs($userA)->postJson(route('attendance.clockIn'), [
            'employee_id' => $employeeB->id,
            'lat' => 3.139015,
            'lng' => 101.686915,
        ]);

        $response->assertStatus(403);
        $this->assertStringContainsString('Unauthorized attendance access for this employee', $response->json('message'));
        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $employeeB->id,
        ]);
    }

    public function test_scenario_f_admin_staff_role_separation_remains_unchanged(): void
    {
        $admin = $this->createAdmin();
        [$staff, $employee] = $this->createStaffWithEmployee();

        // 1. Admin can access admin dashboard and settings
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('settings.index'))->assertStatus(200);

        // 2. Staff is blocked from admin dashboard and redirected/forbidden
        $staffDashRes = $this->actingAs($staff)->get(route('admin.dashboard'));
        $this->assertTrue($staffDashRes->isRedirect() || $staffDashRes->status() === 403);

        $staffSettingsRes = $this->actingAs($staff)->get(route('settings.index'));
        $this->assertTrue($staffSettingsRes->isRedirect() || $staffSettingsRes->status() === 403);

        // 3. Staff can access staff dashboard
        $this->actingAs($staff)->get(route('staff.dashboard'))->assertStatus(200);
    }
}
