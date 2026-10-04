<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — TT Dental Laboratory Attendance System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px 12px;
            background: #080c1e;
            position: relative;
            overflow-x: hidden;
            color: #0f172a;
        }

        /* Ambient Background & Lighting */
        .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .bg-gradient {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% -10%, #1e1b4b 0%, #0c102b 45%, #070a1a 100%);
        }

        .bg-image {
            position: absolute;
            inset: 0;
            background-image: url('{{ asset('images/dental-lab-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            opacity: 0.14;
            filter: contrast(1.1) saturate(1.2);
            mix-blend-mode: screen;
        }

        .bg-dots {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1.2px, transparent 1.2px);
            background-size: 26px 26px;
            opacity: 0.65;
        }

        /* Glowing Orbs */
        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.5;
            animation: orbFloat 14s ease-in-out infinite alternate;
        }
        .bg-orb-1 {
            width: 440px;
            height: 440px;
            background: radial-gradient(circle, #4f46e5 0%, rgba(79, 70, 229, 0) 70%);
            top: -100px;
            left: -80px;
        }
        .bg-orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #9333ea 0%, rgba(147, 51, 234, 0) 70%);
            bottom: -80px;
            right: -60px;
            animation-delay: -6s;
        }
        .bg-orb-3 {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, #2563eb 0%, rgba(37, 99, 235, 0) 70%);
            top: 40%;
            right: 15%;
            animation-delay: -3s;
        }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(25px, 15px) scale(1.04); }
            100% { transform: translate(-15px, 25px) scale(0.96); }
        }

        /* Content Container */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Brand Header (Above Card) */
        .brand-header {
            text-align: center;
            margin-bottom: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .brand-logo-frame {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.98);
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.2), 0 0 24px rgba(99, 102, 241, 0.28);
            margin-bottom: 8px;
            transition: transform 0.22s ease;
        }

        .brand-logo-frame:hover {
            transform: translateY(-2px) scale(1.02);
        }

        .brand-logo-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-name {
            font-size: 16px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            line-height: 1.2;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        }

        .system-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
            padding: 3px 10px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(147, 197, 253, 0.22);
            border-radius: 999px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .system-pill-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #38bdf8;
            box-shadow: 0 0 6px #38bdf8;
        }

        .system-pill-text {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #e2e8f0;
            text-transform: uppercase;
        }

        /* Compact Main Login Card */
        .login-card {
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            padding: 24px 28px 20px;
            box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            position: relative;
        }

        /* Card Header */
        .card-header-block {
            text-align: center;
            margin-bottom: 16px;
        }

        .welcome-title {
            font-size: 21px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.4px;
            line-height: 1.2;
            margin-bottom: 3px;
        }

        .gradient-accent {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .welcome-subtitle {
            font-size: 12.5px;
            color: #64748b;
            font-weight: 500;
        }

        /* Alerts */
        .alert-box {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 14px;
            line-height: 1.4;
        }

        .alert-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        .alert-box svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 13px;
        }

        .form-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .input-icon svg {
            width: 16px;
            height: 16px;
        }

        .form-input {
            width: 100%;
            height: 42px;
            padding: 0 40px 0 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 11px;
            background: #f8fafc;
            color: #0f172a;
            font-size: 13.5px;
            font-weight: 500;
            font-family: inherit;
            outline: none;
            transition: all 0.18s ease;
        }

        .form-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .form-input:focus {
            background: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .form-input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: #4f46e5;
        }

        .toggle-password-btn {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            color: #94a3b8;
            padding: 5px;
            cursor: pointer;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease, background 0.15s ease;
        }

        .toggle-password-btn:hover {
            color: #334155;
            background: #f1f5f9;
        }

        .toggle-password-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Remember & Forgot Row */
        .form-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 0 16px;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            user-select: none;
        }

        .remember-wrap input[type="checkbox"] {
            width: 15px;
            height: 15px;
            border-radius: 4px;
            accent-color: #4f46e5;
            cursor: pointer;
        }

        .remember-text {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .forgot-link {
            font-size: 12px;
            font-weight: 700;
            color: #4f46e5;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .forgot-link:hover {
            color: #3730a3;
            text-decoration: underline;
        }

        /* Sign In Button */
        .btn-signin {
            width: 100%;
            height: 44px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%);
            color: #ffffff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.35);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-signin::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-signin:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 26px rgba(79, 70, 229, 0.45);
        }

        .btn-signin:hover::before {
            left: 100%;
        }

        .btn-signin:active {
            transform: translateY(0);
        }

        .btn-signin svg {
            width: 16px;
            height: 16px;
            stroke-width: 2.3;
            transition: transform 0.2s ease;
        }

        .btn-signin:hover svg {
            transform: translateX(2px);
        }

        /* Security Assurance Inside Card */
        .card-security-badge {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
        }

        .card-security-badge svg {
            width: 13px;
            height: 13px;
            color: #10b981;
        }

        /* Feature Highlights Under Card */
        .features-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            width: 100%;
            margin-top: 14px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 6px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            transition: all 0.18s ease;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(147, 197, 253, 0.25);
            transform: translateY(-1px);
        }

        .feature-icon-wrap {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            flex-shrink: 0;
        }

        .feature-item:nth-child(1) .feature-icon-wrap { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .feature-item:nth-child(2) .feature-icon-wrap { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        .feature-item:nth-child(3) .feature-icon-wrap { background: rgba(168, 85, 247, 0.2); color: #c084fc; }

        .feature-title {
            font-size: 11px;
            font-weight: 700;
            color: #e2e8f0;
            white-space: nowrap;
        }

        /* Compact Footer */
        .page-footer {
            margin-top: 14px;
            text-align: center;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.45);
            line-height: 1.5;
        }

        .footer-copy {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.55);
        }

        .footer-version {
            display: inline-block;
            margin-top: 2px;
            font-size: 10.5px;
            color: rgba(255, 255, 255, 0.35);
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 20px 18px 16px;
                border-radius: 18px;
            }
            .brand-name {
                font-size: 15px;
            }
            .features-strip {
                grid-template-columns: 1fr;
                gap: 6px;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient Background Elements -->
    <div class="bg-layer">
        <div class="bg-gradient"></div>
        <div class="bg-image"></div>
        <div class="bg-dots"></div>
        <div class="bg-orb bg-orb-1"></div>
        <div class="bg-orb bg-orb-2"></div>
        <div class="bg-orb bg-orb-3"></div>
    </div>

    <!-- Main Container -->
    <div class="login-wrapper">

        <!-- TT Dental Laboratory Logo & System Header -->
        <header class="brand-header">
            <div class="brand-logo-frame">
                <img src="{{ asset('images/tt-dental-logo.png') }}" alt="TT Dental Laboratory Logo"
                     onerror="this.style.display='none';this.parentElement.innerHTML='<svg width=\'28\' height=\'28\' fill=\'none\' stroke=\'#4f46e5\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4\'></path></svg>'">
            </div>
            <h1 class="brand-name">TT Dental Laboratory</h1>
            <div class="system-pill">
                <span class="system-pill-dot"></span>
                <span class="system-pill-text">ATTENDANCE MANAGEMENT SYSTEM</span>
            </div>
        </header>

        <!-- White / Light Login Card -->
        <div class="login-card">

            <div class="card-header-block">
                <h2 class="welcome-title">Welcome <span class="gradient-accent">Back</span></h2>
                <p class="welcome-subtitle">Sign in to your Staff Portal & attendance tracking</p>
            </div>

            <!-- Flash Success Message -->
            @if(session('success'))
                <div class="alert-box alert-success" role="alert">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <!-- Error Feedback -->
            @if($errors->any())
                <div class="alert-box alert-error" role="alert">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.submit') }}" method="POST" id="loginForm">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label class="form-label" for="email">
                        <span>Email Address</span>
                    </label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="staff@ttdental.com"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            autofocus
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label" for="password">
                        <span>Password</span>
                    </label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password-btn" id="togglePw" aria-label="Show or hide password">
                            <svg id="eyeOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eyeClosed" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-meta-row">
                    <label class="remember-wrap">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="remember-text">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>

                <!-- Sign In Button -->
                <button type="submit" class="btn-signin" id="submitBtn">
                    <span>Sign In to Staff Portal</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <!-- Card Bottom Security Indicator -->
            <div class="card-security-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>End-to-end encrypted session • Anti-tamper token</span>
            </div>

        </div>

        <!-- SaaS Feature Highlights -->
        <div class="features-strip">
            <div class="feature-item">
                <div class="feature-icon-wrap">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <span class="feature-title">Secure Access</span>
            </div>

            <div class="feature-item">
                <div class="feature-icon-wrap">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="feature-title">Easy Attendance</span>
            </div>

            <div class="feature-item">
                <div class="feature-icon-wrap">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="feature-title">Better Management</span>
            </div>
        </div>

        <!-- Compact Footer -->
        <footer class="page-footer">
            <div class="footer-copy">© {{ date('Y') }} TT Dental Laboratory Sdn Bhd</div>
            <div class="footer-version">Attendance Management System • v2.4</div>
        </footer>

    </div>

    <!-- Scripts -->
    <script>
        // Password visibility toggle
        const togglePw = document.getElementById('togglePw');
        const pwInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        if (togglePw && pwInput) {
            togglePw.addEventListener('click', () => {
                const isPassword = pwInput.type === 'password';
                pwInput.type = isPassword ? 'text' : 'password';
                eyeOpen.style.display = isPassword ? 'none' : 'block';
                eyeClosed.style.display = isPassword ? 'block' : 'none';
            });
        }

        // Form submit state
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.85';
                submitBtn.innerHTML = `
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" style="animation: spin 0.8s linear infinite;">
                        <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="2.5"/>
                        <path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                    <span>Authenticating…</span>
                `;
            });
        }
    </script>
</body>
</html>