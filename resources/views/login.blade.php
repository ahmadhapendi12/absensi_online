<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Absensi Lazatto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <div class="min-h-screen flex">
        
        <!-- Sisi Kiri (Biru Lazatto - Disembunyikan di HP) -->
        <div class="hidden md:flex md:w-1/2 bg-red-900 text-white p-12 flex-col justify-center relative overflow-hidden">
            <div class="relative z-10 max-w-md mx-auto">
                <div class="mb-6 flex items-center">
                    <img src="{{ asset('lazatto-logo.png') }}" alt="Logo Lazatto" class="w-48 object-contain">
                </div>
                <h2 class="text-3xl font-bold mb-4">E-Absensi Lazatto</h2>
                <p class="text-red-200 mb-12">Presensi lebih mudah, aman dan akurat dengan teknologi Face ID & Geofencing.</p>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center border border-white/20"><i class="fas fa-expand text-xl"></i></div>
                        <div><h4 class="font-bold">Face ID</h4><p class="text-sm text-red-200">Pengenalan wajah real-time</p></div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center border border-white/20"><i class="fas fa-map-marker-alt text-xl"></i></div>
                        <div><h4 class="font-bold">Geofencing</h4><p class="text-sm text-red-200">Lokasi sesuai radius cabang</p></div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center border border-white/20"><i class="fas fa-shield-alt text-xl"></i></div>
                        <div><h4 class="font-bold">Akses Terbatas</h4><p class="text-sm text-red-200">Superadmin & Karyawan</p></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan (Form Login) -->
        <div class="w-full md:w-1/2 flex items-center justify-center bg-white p-8">
            <div class="w-full max-w-md">
                
                <!-- Logo versi Mobile -->
                <div class="md:hidden flex items-center justify-center mb-8">
                    <img src="{{ asset('lazatto-logo.png') }}" alt="Logo Lazatto" class="w-40 object-contain">
                </div>

                <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                <p class="text-gray-500 mb-8">Silakan login untuk melanjutkan</p>

                @if($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold border border-red-100 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="far fa-user text-gray-400"></i>
                            </div>
                            <input type="text" name="login" value="{{ old('login') }}" placeholder="Email / NIK" required class="w-full pl-11 pr-4 py-4 border border-gray-200 rounded-xl focus:ring-red-500 focus:border-red-500 bg-gray-50 text-sm">
                        </div>
                    </div>

                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" name="password" placeholder="Password" required class="w-full pl-11 pr-4 py-4 border border-gray-200 rounded-xl focus:ring-red-500 focus:border-red-500 bg-gray-50 text-sm">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <i class="far fa-eye text-gray-400 cursor-pointer hover:text-gray-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500 w-4 h-4">
                            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                        </label>
                        <a href="#" class="text-sm font-semibold text-red-600 hover:text-red-500">Lupa password?</a>
                    </div>

                    <button type="submit" class="w-full bg-red-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-red-700 active:scale-95 transition-all mt-4">
                        Masuk
                    </button>
                </form>
                
                <p class="text-center text-xs text-gray-400 mt-12">© 2026 Lazatto. All rights reserved.</p>
            </div>
        </div>
        
    </div>

</body>
</html>