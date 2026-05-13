<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <title>Login - {{ $appName }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            background-color: #ffffff;
            overflow: hidden;
        }

        .login-header {
            background-color: #2d6a4f;
            /* Green Tahfidz Theme */
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .login-header h4 {
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #2d6a4f;
            box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.1);
        }

        .btn-login {
            background-color: #2d6a4f;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            width: 100%;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background-color: #1b4332;
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background-color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #2d6a4f;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .error-message {
            font-size: 0.85rem;
            color: #dc3545;
            margin-top: 5px;
        }

        .input-group-text {
            background-color: transparent;
            border-left: none;
            border-radius: 0 10px 10px 0;
            color: #718096;
            cursor: pointer;
        }

        .password-input {
            border-right: none;
            border-radius: 10px 0 0 10px !important;
        }

        /* Responsive Mobile */
        @media (max-width: 480px) {
            body {
                background-color: #ffffff;
                align-items: flex-start;
                padding: 20px;
            }

            .login-card {
                box-shadow: none;
                max-width: 100%;
            }

            .login-header {
                padding: 30px 20px;
                border-radius: 16px;
            }

            .login-body {
                padding: 30px 10px;
            }
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">
                T
            </div>
            <h4>{{ $appName }}</h4>
            <p>Silakan masuk ke akun Anda</p>
        </div>
        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3 mb-4 py-2" style="font-size: 0.85rem;">
                    <ul class="mb-0 px-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <input type="password"
                            class="form-control password-input @error('password') is-invalid @enderror" id="password"
                            name="password" placeholder="••••••••" required>
                        <span class="input-group-text" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember" style="font-size: 0.85rem; color: #718096;">
                            Ingat Saya
                        </label>
                    </div>
                    {{-- <a href="#" class="text-decoration-none" style="font-size: 0.85rem; color: #2d6a4f; font-weight: 500;">Lupa Password?</a> --}}
                </div>
                <button type="submit" class="btn-login">Masuk Sekarang</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // toggle the eye icon
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>
