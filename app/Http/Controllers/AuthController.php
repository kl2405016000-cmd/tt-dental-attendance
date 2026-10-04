<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'staff.dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        if (User::count() === 0) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@ttdental.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            return redirect()->intended(
                $user->isAdmin() ? route('admin.dashboard') : route('staff.dashboard')
            );
        }

        // Support Admin login via either their registered Gmail or legacy admin@ttdental.com
        $emailInput = Str::lower(trim($request->string('email')->toString()));
        $adminConfigEmail = Str::lower(trim((string) config('mail.mailers.admin_smtp.username')));
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser && ($emailInput === 'admin@ttdental.com' || ($adminConfigEmail && $emailInput === $adminConfigEmail))) {
            if (Hash::check($request->string('password')->toString(), $adminUser->password)) {
                Auth::login($adminUser, $request->boolean('remember'));
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'));
            }
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function showForgotPassword()
    {
        return view('forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $email = Str::lower(trim($request->string('email')->toString()));
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        $adminConfigEmail = Str::lower(trim((string) config('mail.mailers.admin_smtp.username')));

        // If user not found directly, check if email matches the Admin Gmail address or legacy admin email
        if (!$user) {
            if ($adminConfigEmail && $email === $adminConfigEmail) {
                $user = User::where('role', 'admin')->first();
            } elseif ($email === 'admin@ttdental.com') {
                $user = User::where('role', 'admin')->first();
            }
        }

        // Clear any previous reset flow before starting a new one.
        $request->session()->forget([
            'password_reset_user_id',
            'password_reset_otp_hash',
            'password_reset_otp_expires_at',
            'password_reset_otp_attempts',
            'password_reset_verified_until',
        ]);

        // Do not reveal whether an email exists (security best practice).
        if (!$user) {
            return back()->with('success', 'If an account exists with this email, a verification code has been sent.');
        }

        // Determine destination email
        $recipientEmail = $user->email;
        if ($user->isAdmin() && $adminConfigEmail && ($email === $adminConfigEmail || $recipientEmail === 'admin@ttdental.com')) {
            $recipientEmail = $adminConfigEmail;
        }

        $otp = (string) random_int(100000, 999999);

        $request->session()->put([
            'password_reset_user_id' => $user->id,
            'password_reset_otp_hash' => Hash::make($otp),
            'password_reset_otp_expires_at' => now()->addMinutes(15)->timestamp,
            'password_reset_otp_attempts' => 0,
        ]);

        $isAdmin = $user->isAdmin();
        $mailable = new ResetPasswordOtpMail($otp, $isAdmin);

        try {
            if ($isAdmin && config('mail.mailers.admin_smtp.username')) {
                Mail::mailer('admin_smtp')->to($recipientEmail)->send($mailable);
            } else {
                Mail::to($recipientEmail)->send($mailable);
            }
        } catch (\Throwable $e) {
            // Mask any passwords, app passwords, credentials, tokens, or OTP codes from log
            $safeError = preg_replace('/(password|pass|secret|key|token|auth)=[^&\s]+/i', '$1=***', $e->getMessage());
            $safeError = str_replace($otp, '******', $safeError);
            if (config('mail.mailers.admin_smtp.password')) {
                $safeError = str_replace(config('mail.mailers.admin_smtp.password'), '******', $safeError);
            }
            if (config('mail.mailers.smtp.password')) {
                $safeError = str_replace(config('mail.mailers.smtp.password'), '******', $safeError);
            }

            \Illuminate\Support\Facades\Log::error('OTP Mail Error for user ID ' . $user->id . ' (' . ($isAdmin ? 'Admin' : 'Staff') . '): ' . $safeError);

            // Clear the OTP data since it failed to send
            $request->session()->forget([
                'password_reset_user_id',
                'password_reset_otp_hash',
                'password_reset_otp_expires_at',
                'password_reset_otp_attempts',
            ]);

            return back()->with('success', 'If an account exists with this email, a verification code has been sent.');
        }

        return redirect()
            ->route('password.verify')
            ->with('otp_email', $recipientEmail)
            ->with('success', 'A secure 6-digit verification code has been sent to your registered email.');
    }

    public function showVerifyOtp(Request $request)
    {
        if (!$request->session()->has('password_reset_user_id')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $session = $request->session();
        $userId = $session->get('password_reset_user_id');
        $otpHash = $session->get('password_reset_otp_hash');
        $expiresAt = (int) $session->get('password_reset_otp_expires_at', 0);
        $attempts = (int) $session->get('password_reset_otp_attempts', 0);

        if (!$userId || !$otpHash || !$expiresAt) {
            return redirect()->route('password.request')->withErrors(['otp' => 'This verification session has expired. Please request a new OTP.']);
        }

        if (now()->timestamp > $expiresAt) {
            $session->forget([
                'password_reset_user_id',
                'password_reset_otp_hash',
                'password_reset_otp_expires_at',
                'password_reset_otp_attempts',
            ]);
            return redirect()->route('password.request')->withErrors(['email' => 'The OTP has expired. Please request a new one.']);
        }

        if ($attempts >= 5) {
            $session->forget([
                'password_reset_user_id',
                'password_reset_otp_hash',
                'password_reset_otp_expires_at',
                'password_reset_otp_attempts',
            ]);
            return redirect()->route('password.request')->withErrors(['email' => 'Too many verification attempts. Please request a new OTP.']);
        }

        $session->increment('password_reset_otp_attempts');

        if (!Hash::check($request->string('otp')->toString(), $otpHash)) {
            $remaining = max(0, 5 - (int) $session->get('password_reset_otp_attempts', 0));
            return back()->withErrors(['otp' => "Invalid OTP. {$remaining} attempt(s) remaining."]);
        }

        // OTP is one-time: destroy the OTP material immediately after verification.
        $session->forget([
            'password_reset_otp_hash',
            'password_reset_otp_expires_at',
            'password_reset_otp_attempts',
        ]);
        $session->put('password_reset_verified_until', now()->addMinutes(10)->timestamp);

        return redirect()->route('password.reset')->with('success', 'OTP verified successfully. You can now create a new password.');
    }

    public function showResetPassword(Request $request)
    {
        $userId = $request->session()->get('password_reset_user_id');
        $verifiedUntil = (int) $request->session()->get('password_reset_verified_until', 0);

        if (!$userId || !$verifiedUntil || now()->timestamp > $verifiedUntil) {
            return redirect()->route('password.request')->withErrors(['email' => 'Your password reset session has expired. Please request a new OTP.']);
        }

        return view('auth.reset-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $userId = $request->session()->get('password_reset_user_id');
        $verifiedUntil = (int) $request->session()->get('password_reset_verified_until', 0);

        if (!$userId || !$verifiedUntil || now()->timestamp > $verifiedUntil) {
            return redirect()->route('password.request')->withErrors(['email' => 'Your password reset session has expired. Please request a new OTP.']);
        }

        $user = User::find($userId);
        if (!$user) {
            $request->session()->invalidate();
            return redirect()->route('login')->withErrors(['email' => 'Unable to complete the password reset.']);
        }

        $user->password = Hash::make($request->string('password')->toString());
        $user->save();

        // Invalidate the reset flow before authenticating the user.
        $request->session()->forget([
            'password_reset_user_id',
            'password_reset_otp_hash',
            'password_reset_otp_expires_at',
            'password_reset_otp_attempts',
            'password_reset_verified_until',
            'otp_email',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'staff.dashboard')->with('success', 'Your password has been changed successfully.');
    }
}
