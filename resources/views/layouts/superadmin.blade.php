<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Superadmin - Lazatto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#F4F7FE] flex font-sans text-gray-800 antialiased">

    <!-- SIDEBAR KIRI -->
    <aside class="w-72 bg-[#0B1437] min-h-screen fixed left-0 top-0 text-gray-300 shadow-xl flex flex-col">
        <!-- Logo -->
        <div class="p-8 border-b border-gray-700/50">
            <h2 class="text-2xl font-bold text-white flex items-center">
                <i class="fas fa-fingerprint text-yellow-500 mr-3 text-3xl"></i> Lazatto
            </h2>
            <p class="text-xs text-gray-400 mt-2">Sistem Presensi Terpusat</p>
        </div>
        
        <!-- PROFIL SUPERADMIN (Di dalam Sidebar) -->
        <div class="p-6 border-b border-gray-700/50 flex items-center gap-4 bg-white/5 mx-4 mt-6 rounded-2xl">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=EBF8FF&color=2B6CB0" class="w-12 h-12 rounded-full border-2 border-blue-500">
            <div class="overflow-hidden">
                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-blue-400 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <nav class="flex-1 p-4 space-y-2 mt-4 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-xl hover:bg-blue-600 hover:text-white transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : '' }}">
                <i class="fas fa-home w-8 text-lg"></i> Dashboard
            </a>
            <a href="{{ route('superadmin.karyawan.index') }}" class="flex items-center p-3 rounded-xl hover:bg-blue-600 hover:text-white transition-all {{ request()->routeIs('superadmin.karyawan.*') ? 'bg-blue-600 text-white' : '' }}">
                <i class="fas fa-users w-8 text-lg"></i> Kelola Karyawan
            </a>
            <a href="{{ route('superadmin.cabang.index') }}" class="flex items-center p-3 rounded-xl hover:bg-blue-600 hover:text-white transition-all {{ request()->routeIs('superadmin.cabang.*') ? 'bg-blue-600 text-white' : '' }}">
                <i class="fas fa-map-marker-alt w-8 text-lg"></i> Kelola Cabang
            </a>
            <a href="{{ route('superadmin.pengajuan.index') }}" class="flex items-center p-3 rounded-xl hover:bg-blue-600 hover:text-white transition-all {{ request()->routeIs('superadmin.pengajuan.*') ? 'bg-blue-600 text-white' : '' }}">
                <i class="fas fa-envelope-open-text w-8 text-lg"></i> Pengajuan Izin
            </a>
            <a href="{{ route('superadmin.laporan.index') }}" class="flex items-center p-3 rounded-xl hover:bg-blue-600 hover:text-white transition-all {{ request()->routeIs('superadmin.laporan.*') ? 'bg-blue-600 text-white' : '' }}">
                <i class="fas fa-file-pdf w-8 text-lg"></i> Laporan PDF
            </a>
        </nav>

        <!-- Tombol Logout -->
        <div class="p-4 mb-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center p-3 rounded-xl text-red-400 hover:bg-red-500 hover:text-white transition-all border border-red-500/30 hover:border-transparent">
                    <i class="fas fa-sign-out-alt w-8 text-lg"></i> Keluar Aplikasi
                </button>
            </form>
        </div>
    </aside>

    <!-- AREA KONTEN UTAMA (Berada di sebelah kanan sidebar) -->
    <main class="ml-72 flex-1 min-h-screen flex flex-col">
        <!-- Header Atas -->
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 px-8 py-5 flex justify-between items-center border-b border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-[#2B3674]">@yield('title', 'Dashboard Superadmin')</h1>
                <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            
            <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-50">
                <i class="fas fa-bell text-gray-400 hover:text-blue-500 cursor-pointer transition"></i>
                <div class="w-px h-6 bg-gray-200"></div>
                <span class="text-sm font-bold text-gray-700">Superadmin</span>
            </div>
        </header>

        <!-- Tempat Konten (Tabel dll) Disuntikkan -->
        <div class="p-8 flex-1">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>