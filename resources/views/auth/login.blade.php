<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Perpus Sandikta</title>
    <link rel="icon" href="https://sandikta.sch.id/wp-content/uploads/2024/04/Logo-Sandikta-png.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 40%, #1e40af 70%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background */
        .bg-shapes {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .bg-shapes .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            animation: float 20s infinite ease-in-out;
        }

        .bg-shapes .shape:nth-child(1) {
            width: 600px;
            height: 600px;
            top: -200px;
            right: -100px;
            animation-delay: 0s;
        }

        .bg-shapes .shape:nth-child(2) {
            width: 400px;
            height: 400px;
            bottom: -150px;
            left: -100px;
            animation-delay: 5s;
        }

        .bg-shapes .shape:nth-child(3) {
            width: 300px;
            height: 300px;
            top: 50%;
            left: 50%;
            animation-delay: 10s;
        }

        .bg-shapes .shape:nth-child(4) {
            width: 200px;
            height: 200px;
            top: 20%;
            left: 20%;
            background: rgba(96, 165, 250, 0.05);
            animation-delay: 3s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            33% {
                transform: translateY(-30px) rotate(5deg);
            }

            66% {
                transform: translateY(20px) rotate(-3deg);
            }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-brand {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-brand .icon-wrap {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            border-radius: 20px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.4);
            animation: pulse 3s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 8px 30px rgba(59, 130, 246, 0.4);
            }

            50% {
                box-shadow: 0 8px 40px rgba(59, 130, 246, 0.6);
            }
        }

        .login-brand .icon-wrap i {
            font-size: 32px;
            color: #fff;
        }

        .login-brand h2 {
            color: #fff;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .login-brand p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            margin-top: 6px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .input-group-modern {
            position: relative;
        }

        .input-group-modern i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            font-size: 18px;
            z-index: 5;
        }

        .input-group-modern input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: rgba(255, 255, 255, 0.06);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: #fff;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .input-group-modern input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .input-group-modern input:focus {
            outline: none;
            border-color: rgba(96, 165, 250, 0.6);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.35);
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 12px 16px;
            color: #fca5a5;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <div class="login-container">
        <div class="login-card">
            <div class="login-brand">
                <img src="{{ asset('https://sandikta.sch.id/wp-content/uploads/2024/04/Logo-Sandikta-png.png') }}"
                    alt="Logo" style="width: 120px; height: 120px; display: block; margin: 0 auto;">
                <h2>Perpus Sandikta</h2>
                <p>Perpustakaan Digital Sekolah</p>
            </div>

            @if($errors->any())
                <div class="error-msg">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('success'))
                <div
                    style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:12px;padding:12px 16px;color:#6ee7b7;font-size:13px;margin-bottom:20px;">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div
                    style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);border-radius:12px;padding:12px 16px;color:#fbbf24;font-size:13px;margin-bottom:20px;">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="form-group">
                    <label>NIS / Email</label>
                    <div class="input-group-modern">
                        <i class="bi bi-person"></i>
                        <input type="text" name="username" value="{{ old('username') }}"
                            placeholder="Masukkan NIS atau Email" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group-modern">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>
            <div class="login-footer">
                &copy; {{ date('Y') }} Perpus Sandikta. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>