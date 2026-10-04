<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - TT Dental Lab</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            min-height: 100vh;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px 20px;

            background:
                linear-gradient(
                    135deg,
                    #020617 0%,
                    #0b1730 50%,
                    #020617 100%
                );

        }


        .login-container {

            width: 100%;

            max-width: 650px;

        }


        .login-card {

            background: white;

            border-radius: 28px;

            padding: 50px 60px;

            box-shadow:
                0 30px 80px
                rgba(0, 0, 0, 0.45);

        }


        /* =========================
           LOGO
        ========================= */

        .logo-area {

            text-align: center;

            margin-bottom: 30px;

        }


        .logo-circle {

            width: 165px;

            height: 165px;

            margin: 0 auto 22px;

            border-radius: 50%;

            overflow: hidden;

            background: white;

            border: 4px solid white;

            box-shadow:
                0 0 0 2px #dbeafe,
                0 12px 35px
                rgba(37, 99, 235, 0.25);

        }


        .logo-circle img {

            width: 100%;

            height: 100%;

            object-fit: cover;

            display: block;

        }


        .logo-area h1 {

            margin: 0;

            color: #07142f;

            font-size: 38px;

            font-weight: 800;

        }


        .subtitle {

            margin-top: 10px;

            color: #64748b;

            font-size: 13px;

            font-weight: bold;

            letter-spacing: 3px;

        }


        /* =========================
           ERROR
        ========================= */

        .error {

            background: #fee2e2;

            color: #991b1b;

            border:
                1px solid #fecaca;

            padding: 13px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 14px;

        }


        /* =========================
           FORM
        ========================= */

        .form-group {

            margin-bottom: 20px;

        }


        label {

            display: block;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: bold;

            color: #374151;

        }


        .input-wrapper {

            position: relative;

        }


        .input-icon {

            position: absolute;

            left: 18px;

            top: 50%;

            transform: translateY(-50%);

            font-size: 20px;

        }


        input {

            width: 100%;

            height: 60px;

            padding:
                0 50px;

            border:
                1px solid #cbd5e1;

            border-radius: 14px;

            font-size: 15px;

            outline: none;

        }


        input:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 4px
                rgba(37, 99, 235, 0.10);

        }


        /* =========================
           PASSWORD EYE
        ========================= */

        .toggle-password {

            position: absolute;

            right: 15px;

            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            cursor: pointer;

            font-size: 20px;

        }


        /* =========================
           OPTIONS
        ========================= */

        .options {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin:
                5px 3px 25px;

        }


        .remember {

            display: flex;

            align-items: center;

            gap: 8px;

            font-size: 13px;

            color: #64748b;

        }


        .remember input {

            width: 17px;

            height: 17px;

        }


        .forgot-password {

            color: #2563eb;

            text-decoration: none;

            font-size: 13px;

            font-weight: bold;

        }


        .forgot-password:hover {

            text-decoration: underline;

        }


        /* =========================
           LOGIN BUTTON
        ========================= */

        .login-btn {

            width: 100%;

            height: 60px;

            border: none;

            border-radius: 15px;

            background:
                linear-gradient(
                    135deg,
                    #071b43,
                    #020617
                );

            color: white;

            font-size: 16px;

            font-weight: bold;

            letter-spacing: 2px;

            cursor: pointer;

            transition: 0.2s;

        }


        .login-btn:hover {

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px
                rgba(2, 6, 23, 0.30);

        }


        /* =========================
           DIVIDER
        ========================= */

        .divider {

            display: flex;

            align-items: center;

            gap: 15px;

            margin: 28px 0;

            color: #94a3b8;

            font-size: 13px;

        }


        .divider::before,
        .divider::after {

            content: "";

            flex: 1;

            height: 1px;

            background: #e2e8f0;

        }


        /* =========================
           BIOMETRIC BUTTON
        ========================= */

        .biometric-btn {

            width: 100%;

            min-height: 95px;

            border: 2px solid #166534;

            border-radius: 17px;

            background:
                linear-gradient(
                    135deg,
                    #f0fdf4,
                    #dcfce7
                );

            color: #166534;

            text-decoration: none;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 18px;

            cursor: pointer;

            transition: 0.2s;

        }


        .biometric-btn:hover {

            transform: translateY(-2px);

            background: #dcfce7;

            box-shadow:
                0 12px 25px
                rgba(22, 101, 52, 0.18);

        }


        .face-icon {

            width: 58px;

            height: 58px;

            border-radius: 50%;

            background: #166534;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 28px;

        }


        .biometric-text {

            text-align: left;

        }


        .biometric-title {

            font-size: 17px;

            font-weight: 800;

        }


        .biometric-subtitle {

            margin-top: 5px;

            font-size: 12px;

            color: #4b5563;

        }


        /* =========================
           SECURITY
        ========================= */

        .security {

            text-align: center;

            margin-top: 28px;

            color: #94a3b8;

            font-size: 12px;

            line-height: 1.7;

        }


        .security strong {

            color: #1746a2;

            letter-spacing: 2px;

        }


        /* =========================
           FOOTER
        ========================= */

        .footer {

            text-align: center;

            margin-top: 22px;

            color: #94a3b8;

            font-size: 11px;

        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 650px) {

            .login-card {

                padding:
                    40px 24px;

            }


            .logo-circle {

                width: 135px;

                height: 135px;

            }


            .logo-area h1 {

                font-size: 30px;

            }


            .subtitle {

                font-size: 10px;

                letter-spacing: 2px;

            }


            .options {

                font-size: 12px;

            }

        }

    </style>

</head>


<body>


<div class="login-container">


    <div class="login-card">


        <!-- =========================
             LOGO
        ========================== -->

        <div class="logo-area">

            <div class="logo-circle">

                <img
                    src="{{ asset('images/tt-dental-logo.png') }}"
                    alt="TT Dental Laboratory Logo"
                >

            </div>


            <h1>
                TT Dental Lab
            </h1>


            <div class="subtitle">

                BIOMETRIC ATTENDANCE SYSTEM

            </div>

        </div>


        <!-- =========================
             ERROR
        ========================== -->

        @if (session('error'))

            <div class="error">

                {{ session('error') }}

            </div>

        @endif


        @if ($errors->any())

            <div class="error">

                {{ $errors->first() }}

            </div>

        @endif


        <!-- =========================
             LOGIN FORM
        ========================== -->

        <form
            action="/login"
            method="POST"
        >

            @csrf


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">

                    Email

                </label>


                <div class="input-wrapper">

                    <span class="input-icon">
                        ✉️
                    </span>


                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        value="{{ old('email') }}"
                        required
                    >

                </div>

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">

                    Password

                </label>


                <div class="input-wrapper">

                    <span class="input-icon">
                        🔒
                    </span>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >


                    <button
                        type="button"
                        class="toggle-password"
                        id="togglePassword"
                    >

                        👁️

                    </button>

                </div>

            </div>


            <!-- OPTIONS -->

            <div class="options">


                <label class="remember">

                    <input
                        type="checkbox"
                        name="remember"
                    >

                    Remember me

                </label>


                <a
                    href="/forgot-password"
                    class="forgot-password"
                >

                    Forgot Password?

                </a>


            </div>


            <!-- LOGIN -->

            <button
                type="submit"
                class="login-btn"
            >

                🔐 &nbsp; LOGIN

            </button>


        </form>


        <!-- =========================
             DIVIDER
        ========================== -->

        <div class="divider">

            OR

        </div>


        <!-- =========================
             BIOMETRIC LOGIN
        ========================== -->

        <a
            href="/face-verification"
            class="biometric-btn"
        >

            <div class="face-icon">

                👤

            </div>


            <div class="biometric-text">

                <div class="biometric-title">

                    SCAN BIOMETRIC

                </div>


                <div class="biometric-subtitle">

                    Use facial recognition to verify your identity

                </div>

            </div>

        </a>


        <!-- =========================
             SECURITY
        ========================== -->

        <div class="security">

            🛡️ Secure Facial Recognition Attendance

            <br>

            <strong>
                TT DENTAL LAB
            </strong>

        </div>


    </div>


    <div class="footer">

        TT Dental Lab © 2026

    </div>


</div>


<script>

    const password =
        document.getElementById('password');


    const togglePassword =
        document.getElementById('togglePassword');


    togglePassword.addEventListener(
        'click',
        function () {

            if (
                password.type === 'password'
            ) {

                password.type = 'text';

                togglePassword.textContent = '🙈';

            } else {

                password.type = 'password';

                togglePassword.textContent = '👁️';

            }

        }
    );

</script>


</body>

</html>