<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>E-Absensi Lazatto</title>
    
    <!-- Memanggil Tailwind CSS bawaan Laravel Breeze -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome untuk Ikon Menu -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Mencegah scroll horizontal di HP */
        body { overflow-x: hidden; background-color: #f3f4f6; }
        /* Area konten utama agar tidak tertutup menu bawah */
        .main-content { padding-bottom: 80px; }
    </style>
    @stack('styles')
</head>
<body class="text-gray-800 antialiased font-sans max-w-md mx-auto bg-white min-h-screen relative shadow-lg">

    <!-- Area Konten Utama yang akan berubah-ubah -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar (Menu Bawah Mobile) -->
    <nav class="fixed bottom-0 w-full max-w-md bg-white border-t border-gray-200 flex justify-around py-3 px-2 z-50 rounded-t-2xl shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
        
        <!-- Menu Beranda -->
        <a href="{{ route('karyawan.presensi.create') }}" class="flex flex-col items-center text-gray-400 hover:text-blue-600 {{ request()->routeIs('karyawan.presensi.*') ? 'text-blue-600' : '' }}">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>

        <!-- Menu Riwayat -->
        <a href="{{ route('karyawan.riwayat.index') }}" class="flex flex-col items-center text-gray-400 hover:text-blue-600 {{ request()->routeIs('karyawan.riwayat.*') ? 'text-blue-600' : '' }}">
            <i class="fas fa-history text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Riwayat</span>
        </a>

        <!-- Menu Pengajuan -->
        <a href="{{ route('karyawan.pengajuan.index') }}" class="flex flex-col items-center text-gray-400 hover:text-blue-600 {{ request()->routeIs('karyawan.pengajuan.*') ? 'text-blue-600' : '' }}">
            <i class="fas fa-file-alt text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Pengajuan</span>
        </a>

        <!-- Menu Profil -->
        <a href="{{ route('karyawan.profil.index') }}" class="flex flex-col items-center text-gray-400 hover:text-blue-600 {{ request()->routeIs('karyawan.profil.*') ? 'text-blue-600' : '' }}">
            <i class="fas fa-user text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
        
    </nav>

    @stack('scripts')
</body>
</html>