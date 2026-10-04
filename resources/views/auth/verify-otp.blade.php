<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - TT Dental Lab</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #090f1d 0%, #0f172a 50%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #0f172a;
        }

        .auth-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 24px;
            padding: 44px 40px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.45);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: white;
            padding: 4px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25);
            border: 2px solid #e0e7ff;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .brand-desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 22px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .otp-input {
            width: 100%;
            height: 60px;
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 12px;
            font-family: monospace;
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            outline: none;
            transition: all 0.2s;
            color: #0f172a;
        }

        .otp-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .btn-submit {
            width: 100%;
            height: 52px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .footer-links {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }

        .footer-links a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 700;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .otp-timer {
            text-align: center;
            font-size: 12px;
            color: #64748b;
            margin-top: 14px;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="brand-header">
        <div class="brand-logo">
            <img src="{{ asset('images/tt-dental-logo.png') }}" alt="TT Dental Lab">
        </div>
        <h1 class="brand-title">Enter Verification Code</h1>
        <p class="brand-desc">
            We sent a 6-digit OTP code to:
            <br>
            <strong>{{ session('otp_email') }}</strong>
        </p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.verify.otp') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="otp">6-Digit One-Time Passcode</label>
            <input
                type="text"
                name="otp"
                id="otp"
                class="otp-input"
                maxlength="6"
                inputmode="numeric"
                pattern="[0-9]{6}"
                placeholder="------"
                autocomplete="one-time-code"
                autofocus
                required
            >
        </div>

        <button type="submit" class="btn-submit">
            Verify Code →
        </button>
    </form>

    <div class="otp-timer">
        ⏱️ Code expires in <strong>15 minutes</strong>. Max 5 attempts.
    </div>

    <div class="footer-links">
        <p>Didn't receive the code?
            <a href="{{ route('password.request') }}">Resend OTP</a>
        </p>
        <p style="margin-top: 8px;">
            <a href="{{ route('login') }}" style="color: #64748b;">← Back to Login</a>
        </p>
    </div>
</div>

</body>
</html>
