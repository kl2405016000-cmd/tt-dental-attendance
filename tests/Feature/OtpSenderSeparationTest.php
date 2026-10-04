<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpSenderSeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_otp_sent_via_default_mailer()
    {
        Mail::fake();

        $staffUser = User::factory()->create([
            'email' => 'staff1@ttdental.com',
            'role' => 'staff',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'staff1@ttdental.com',
        ]);

        $response->assertRedirect(route('password.verify'));

        Mail::assertSent(ResetPasswordOtpMail::class, function ($mail) use ($staffUser) {
            return $mail->hasTo($staffUser->email) && $mail->isAdmin === false;
        });
    }

    public function test_admin_otp_sent_via_admin_smtp_mailer_when_configured()
    {
        Mail::fake();

        Config::set('mail.mailers.admin_smtp.username', 'admin_actual@gmail.com');
        Config::set('mail.admin_from.address', 'admin_actual@gmail.com');
        Config::set('mail.admin_from.name', 'TT Dental Lab (Admin Portal)');

        $adminUser = User::factory()->create([
            'email' => 'admin_actual@gmail.com',
            'role' => 'admin',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'admin_actual@gmail.com',
        ]);

        $response->assertRedirect(route('password.verify'));

        Mail::mailer('admin_smtp')->assertSent(ResetPasswordOtpMail::class, function ($mail) use ($adminUser) {
            $envelope = $mail->envelope();
            return $mail->hasTo($adminUser->email)
                && $mail->isAdmin === true
                && str_contains($envelope->subject, '[Admin Security]')
                && $envelope->from->address === 'admin_actual@gmail.com';
        });
    }

    public function test_admin_can_request_otp_using_configured_gmail_when_db_has_legacy_email()
    {
        Mail::fake();

        Config::set('mail.mailers.admin_smtp.username', 'ttdentalattendance@gmail.com');
        Config::set('mail.admin_from.address', 'ttdentalattendance@gmail.com');

        $adminUser = User::factory()->create([
            'email' => 'admin@ttdental.com',
            'role' => 'admin',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'ttdentalattendance@gmail.com',
        ]);

        $response->assertRedirect(route('password.verify'));

        Mail::mailer('admin_smtp')->assertSent(ResetPasswordOtpMail::class, function ($mail) {
            return $mail->hasTo('ttdentalattendance@gmail.com')
                && $mail->isAdmin === true;
        });
    }

    public function test_admin_otp_falls_back_gracefully_when_admin_mailer_not_configured()
    {
        Mail::fake();

        Config::set('mail.mailers.admin_smtp.username', null);

        $adminUser = User::factory()->create([
            'email' => 'admin@ttdental.com',
            'role' => 'admin',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'admin@ttdental.com',
        ]);

        $response->assertRedirect(route('password.verify'));

        Mail::assertSent(ResetPasswordOtpMail::class, function ($mail) use ($adminUser) {
            return $mail->hasTo($adminUser->email) && $mail->isAdmin === true;
        });
    }

    public function test_admin_login_works_with_either_registered_gmail_or_legacy_alias()
    {
        Config::set('mail.mailers.admin_smtp.username', 'ttdentalattendance@gmail.com');

        $adminUser = User::factory()->create([
            'email' => 'ttdentalattendance@gmail.com',
            'password' => Hash::make('AdminSecret123'),
            'role' => 'admin',
        ]);

        // Login using primary registered Gmail
        $response1 = $this->post(route('login.submit'), [
            'email' => 'ttdentalattendance@gmail.com',
            'password' => 'AdminSecret123',
        ]);
        $response1->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($adminUser);

        $this->post(route('logout'));

        // Login using legacy alias admin@ttdental.com
        $response2 = $this->post(route('login.submit'), [
            'email' => 'admin@ttdental.com',
            'password' => 'AdminSecret123',
        ]);
        $response2->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($adminUser);
    }
}
