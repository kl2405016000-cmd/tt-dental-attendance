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
        $this->assertStringContainsString('outside the workplace boundary', $response->json('message'));

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

        // Attempt break start outside workplace
        $outsideLat = 3.150000;
        $outsideLng = 101.686900;

        $response = $this->actingAs($user)->postJson(route('attendance.startBreak'), [
            'employee_id' => $employee->id,
            'lat' => $outsideLat,
            'lng' => $outsideLng,
        ]);

        $response->assertStatus(403);
        $this->assertStringContainsString('outside the workplace boundary', $response->json('message'));

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
        $this->assertStringContainsString('outside the workplace boundary', $response->json('message'));

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
        $this->assertStringContainsString('outside the workplace boundary', $response->json('message'));

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
}
