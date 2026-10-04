<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset OTP - TT Dental Lab</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 30px; color: #1e293b;">
    <div style="max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 36px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="color: #0f172a; margin: 0 0 6px; font-size: 22px;">TT Dental Laboratory</h2>
            <p style="color: #64748b; margin: 0; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">{{ !empty($isAdmin) ? 'Admin Portal Security Verification' : 'Biometric Attendance System' }}</p>
        </div>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">

        <p style="font-size: 15px; line-height: 1.6; color: #334155;">Hello,</p>
        <p style="font-size: 15px; line-height: 1.6; color: #334155;">We received a request to reset your password. Use the one-time verification code (OTP) below to proceed:</p>

        <div style="text-align: center; margin: 28px 0;">
            <div style="display: inline-block; background: #f0fdf4; border: 2px dashed #16a34a; border-radius: 12px; padding: 16px 36px;">
                <span style="font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #166534; font-family: monospace;">{{ $otp }}</span>
            </div>
        </div>

        <p style="font-size: 13px; line-height: 1.6; color: #64748b; text-align: center;">
            ⏱️ This code is valid for <strong>15 minutes</strong> and can only be used once.
        </p>

        <div style="background: #fff7ed; border-left: 4px solid #f97316; padding: 12px 16px; margin: 24px 0 16px; border-radius: 6px;">
            <p style="margin: 0; font-size: 12px; color: #9a3412; line-height: 1.5;">
                <strong>Security Notice:</strong> If you did not request this password reset, please ignore this email or notify your system administrator immediately. Never share this code with anyone.
            </p>
        </div>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0 16px;">

        <p style="font-size: 11px; color: #94a3b8; text-align: center; margin: 0;">
            © {{ date('Y') }} TT Dental Lab. All rights reserved.
        </p>
    </div>
</body>
</html>
