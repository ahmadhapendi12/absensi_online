<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Absensi Lazatto</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #faf7f2;
            color: #1f2937;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100vw;
        }

        /* SISI KIRI: HERO BANNER */
        .hero-section {
            width: 50%;
            flex: 0 0 50%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(2rem, 4vw, 4rem);
            background: linear-gradient(to bottom, rgba(15, 10, 10, 0.65) 0%, rgba(30, 15, 15, 0.45) 40%, rgba(15, 8, 8, 0.8) 100%),
                        url('{{ asset('background.jpg') }}') center center / cover no-repeat;
            color: #ffffff;
            position: relative;
        }

        .brand-badge-box {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .logo-card {
            width: 44px;
            height: 44px;
            background-color: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .logo-card img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .brand-title {
            color: #ffffff;
            font-weight: 800;
            font-size: clamp(0.85rem, 1.1vw, 1.05rem);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        .hero-main-content {
            margin-top: auto;
            margin-bottom: auto;
            max-width: 580px;
            padding: 1.5rem 0;
        }

        .hero-heading {
            font-size: clamp(2.25rem, 4.2vw, 4.5rem);
            font-weight: 900;
            line-height: 1.12;
            letter-spacing: -0.03em;
            color: #ffffff;
            margin-bottom: 1.25rem;
            text-shadow: 0 3px 12px rgba(0, 0, 0, 0.5);
        }

        .hero-description {
            font-size: clamp(0.95rem, 1.15vw, 1.2rem);
            color: rgba(255, 255, 255, 0.92);
            line-height: 1.6;
            max-width: 520px;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.45);
        }

        .hero-footer-copy {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        /* SISI KANAN: FORM PORTAL */
        .form-section {
            width: 50%;
            flex: 0 0 50%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #faf7f2;
            padding: clamp(1.5rem, 4vw, 4rem);
        }

        .form-box {
            width: 100%;
            max-width: 440px;
        }

        .form-title {
            font-size: 2.25rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.025em;
            margin-bottom: 0.35rem;
        }

        .form-subtitle {
            font-size: 0.95rem;
            color: #94a3b8;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.55rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1.1rem;
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
        }

        .custom-input {
            width: 100%;
            padding: 0.95rem 1rem 0.95rem 2.9rem;
            background-color: #edf4fc;
            border: 1.5px solid #d4e2f4;
            border-radius: 0.85rem;
            font-size: 0.925rem;
            font-family: inherit;
            color: #1e293b;
            outline: none;
            transition: border-color 0.15s, background-color 0.15s, box-shadow 0.15s;
        }

        .custom-input:focus {
            background-color: #ffffff;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.18);
        }

        .toggle-btn {
            position: absolute;
            right: 1rem;
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            font-size: 1rem;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-btn:hover {
            color: #64748b;
        }

        .options-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 0.85rem;
            margin-bottom: 1.75rem;
        }

        .remember-label {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox {
            width: 1rem;
            height: 1rem;
            accent-color: #f97316;
            cursor: pointer;
        }

        .remember-text {
            margin-left: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 700;
            color: #ea580c;
            transition: color 0.15s;
        }

        .remember-label:hover .remember-text {
            color: #c2410c;
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem 1.5rem;
            background-color: #f97316;
            color: #ffffff;
            border: none;
            border-radius: 0.85rem;
            font-size: 0.975rem;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(249, 115, 22, 0.35);
            transition: transform 0.15s, background-color 0.15s, box-shadow 0.15s;
        }

        .btn-submit:hover {
            background-color: #ea580c;
            box-shadow: 0 8px 22px rgba(249, 115, 22, 0.45);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-box {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #b91c1c;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* RESPONSIVE LAYOUT (HP / TABLET) */
        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
            }

            .hero-section {
                display: none; /* Hide on mobile like reference layout */
            }

            .form-section {
                width: 100%;
                flex: 1 1 100%;
                min-height: 100vh;
                padding: 2.5rem 1.5rem;
            }

            .mobile-logo-container {
                display: flex !important;
                align-items: center;
                justify-content: center;
                margin-bottom: 2rem;
            }

            .mobile-logo-img {
                width: 160px;
                max-width: 80%;
                height: auto;
                object-fit: contain;
            }

            .mobile-footer-copy {
                display: block !important;
                text-align: center;
                font-size: 0.75rem;
                color: #9ca3af;
                margin-top: 3rem;
            }
        }

        @media (min-width: 901px) {
            .mobile-logo-container {
                display: none !important;
            }
            .mobile-footer-copy {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">

        <!-- ================= SISI KIRI: HERO BANNER SESUAI REFERENSI ================= -->
        <div class="hero-section">
            
            <!-- Header Atas: Badge Logo & Judul Berdampingan -->
            <div class="brand-badge-box">
                <div class="logo-card">
                    <img src="{{ asset('lazatto-logo.png') }}" alt="Logo Lazatto">
                </div>
                <span class="brand-title">
                    E-ABSENSI LAZATTO
                </span>
            </div>

            <!-- Konten Utama: Judul Besar Multi-baris & Definisi Sistem -->
            <div class="hero-main-content">
                <h1 class="hero-heading">
                    Sistem <br>
                    Manajemen<br>
                    Kehadiran .
                </h1>
                <p class="hero-description">
                    Platform absensi dan presensi karyawan terpadu untuk Lazatto. Pantau kehadiran, Face ID biometrik, dan geofencing secara real-time.
                </p>
            </div>

            <!-- Footer Hak Cipta -->
            <div class="hero-footer-copy">
                © {{ date('Y') }} Lazatto – All rights reserved.
            </div>

        </div>

        <!-- ================= SISI KANAN: FORM LOGIN SESUAI REFERENSI ================= -->
        <div class="form-section">
            <div class="form-box">

                <!-- Logo versi Mobile (Seperti Semula) -->
                <div class="mobile-logo-container">
                    <img src="{{ asset('lazatto-logo.png') }}" alt="Logo Lazatto" class="mobile-logo-img">
                </div>

                <!-- Header Form -->
                <h2 class="form-title">Login</h2>
                <p class="form-subtitle">Silahkan masuk untuk mengakses sistem.</p>

                <!-- Alert Error -->
                @if($errors->any())
                    <div class="alert-box">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <!-- Input Email / Username -->
                    <div style="margin-bottom: 1.25rem;">
                        <label for="loginInput" class="form-label">Email / Username</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input 
                                type="text" 
                                id="loginInput" 
                                name="login" 
                                value="{{ old('login') }}" 
                                placeholder="adminlazatto@gmail.com" 
                                required 
                                autofocus
                                class="custom-input"
                            >
                        </div>
                    </div>

                    <!-- Input Password -->
                    <div>
                        <label for="passwordInput" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                id="passwordInput" 
                                name="password" 
                                placeholder="••••••••" 
                                required 
                                class="custom-input"
                                style="padding-right: 2.85rem;"
                            >
                            <button type="button" id="togglePassword" class="toggle-btn" aria-label="Toggle password visibility">
                                <i id="eyeIcon" class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Ingat Sesi (Sebelah Kanan Warna Oranye) -->
                    <div class="options-bar">
                    </div>

                    <!-- Tombol Masuk Oranye Tebal -->
                    <button type="submit" class="btn-submit">
                        Masuk
                    </button>

                </form>

                <!-- Footer Copyright Mobile -->
                <p class="mobile-footer-copy">© 2026 Lazatto. All rights reserved.</p>

            </div>
        </div>

    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script Toggle Password -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    if (isPassword) {
                        passwordInput.setAttribute('type', 'text');
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.setAttribute('type', 'password');
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    }
                });
            }

            // SweetAlert2 - Error Login
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal!',
                    text: {!! json_encode($errors->first()) !!},
                    confirmButtonColor: '#f97316',
                    confirmButtonText: 'Coba Lagi',
                    background: '#fef7ed',
                    color: '#1f2937',
                    customClass: {
                        popup: 'swal-rounded'
                    }
                });
            @endif

            // SweetAlert2 - Success
            @if(session('swal_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: {!! json_encode(session('swal_success')) !!},
                    confirmButtonColor: '#f97316',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    background: '#f0fdf4',
                    color: '#1f2937',
                    customClass: {
                        popup: 'swal-rounded'
                    }
                });
            @endif

            // SweetAlert2 - Info (Logout)
            @if(session('swal_info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: {!! json_encode(session('swal_info')) !!},
                    confirmButtonColor: '#f97316',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    background: '#eff6ff',
                    color: '#1f2937',
                    customClass: {
                        popup: 'swal-rounded'
                    }
                });
            @endif
        });
    </script>

    <style>
        .swal-rounded {
            border-radius: 1rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
    </style>

</body>
</html>