<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - TT Dental Lab</title>

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

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 22px;
        }

        .form-group {
            margin-bottom: 20px;
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

        input[type="password"] {
            width: 100%;
            height: 52px;
            padding: 0 16px;
            font-size: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            outline: none;
            transition: all 0.2s;
            color: #0f172a;
        }

        input[type="password"]:focus {
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
            margin-top: 10px;
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
        }

        .footer-links a {
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            color: #2563eb;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="brand-header">
        <div class="brand-logo">
            <img src="{{ asset('images/tt-dental-logo.png') }}" alt="TT Dental Lab">
        </div>
        <h1 class="brand-title">Create New Password</h1>
        <p class="brand-desc">
            Identity verified for <strong>{{ session('reset_password_email') }}</strong>.
            Please choose a strong new password.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="password">New Password (min 8 characters)</label>
            <input
                type="password"
                name="password"
                id="password"
                placeholder="Enter new password"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                placeholder="Confirm new password"
                required
            >
        </div>

        <button type="submit" class="btn-submit">
            Save New Password & Log In →
        </button>
    </form>

    <div class="footer-links">
        <a href="{{ route('login') }}">← Cancel & Back to Login</a>
    </div>
</div>

</body>
</html>
