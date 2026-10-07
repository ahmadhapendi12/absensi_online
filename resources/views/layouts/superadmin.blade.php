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
    <aside class="w-72 bg-red-900 min-h-screen fixed left-0 top-0 text-gray-300 shadow-xl flex flex-col">
        <!-- Logo -->
        <div class="p-6 border-b border-red-800/50 flex justify-center">
            <img src="{{ asset('lazatto-logo.png') }}" alt="Logo Lazatto" class="w-40 object-contain">
        </div>
        
        <!-- PROFIL SUPERADMIN (Di dalam Sidebar) -->
        <div class="p-6 border-b border-red-800/50 flex items-center gap-4 bg-white/5 mx-4 mt-6 rounded-2xl">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=FFE5E5&color=C53030" class="w-12 h-12 rounded-full border-2 border-red-500">
            <div class="overflow-hidden">
                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-red-300 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <nav class="flex-1 p-4 space-y-2 mt-4 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-xl hover:bg-red-600 hover:text-white transition-all {{ request()->routeIs('dashboard') ? 'bg-red-600 text-white' : '' }}">
                <i class="fas fa-home w-8 text-lg"></i> Dashboard
            </a>
            <a href="{{ route('superadmin.karyawan.index') }}" class="flex items-center p-3 rounded-xl hover:bg-red-600 hover:text-white transition-all {{ request()->routeIs('superadmin.karyawan.*') ? 'bg-red-600 text-white' : '' }}">
                <i class="fas fa-users w-8 text-lg"></i> Kelola Karyawan
            </a>
            <a href="{{ route('superadmin.cabang.index') }}" class="flex items-center p-3 rounded-xl hover:bg-red-600 hover:text-white transition-all {{ request()->routeIs('superadmin.cabang.*') ? 'bg-red-600 text-white' : '' }}">
                <i class="fas fa-map-marker-alt w-8 text-lg"></i> Kelola Cabang
            </a>
            <a href="{{ route('superadmin.jadwal.index') }}" class="flex items-center p-3 rounded-xl hover:bg-red-600 hover:text-white transition-all {{ request()->routeIs('superadmin.jadwal.*') ? 'bg-red-600 text-white' : '' }}">
                <i class="fas fa-calendar-alt w-8 text-lg"></i> Jadwal Shift
            </a>
            <a href="{{ route('superadmin.pengajuan.index') }}" class="flex items-center p-3 rounded-xl hover:bg-red-600 hover:text-white transition-all {{ request()->routeIs('superadmin.pengajuan.*') ? 'bg-red-600 text-white' : '' }}">
                <i class="fas fa-envelope-open-text w-8 text-lg"></i> Pengajuan Izin
            </a>
            <a href="{{ route('superadmin.laporan.index') }}" class="flex items-center p-3 rounded-xl hover:bg-red-600 hover:text-white transition-all {{ request()->routeIs('superadmin.laporan.*') ? 'bg-red-600 text-white' : '' }}">
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
                <h1 class="text-2xl font-bold text-red-900">@yield('title', 'Dashboard Superadmin')</h1>
                <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            
            <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-50">
                <i class="fas fa-bell text-gray-400 hover:text-red-500 cursor-pointer transition"></i>
                <div class="w-px h-6 bg-gray-200"></div>
                <span class="text-sm font-bold text-gray-700">Superadmin</span>
            </div>
        </header>

        <!-- Tempat Konten (Tabel dll) Disuntikkan -->
        <div class="p-8 flex-1">
            @yield('content')
        </div>
    </main>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Global SweetAlert untuk Flash Message -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#DC2626',
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#DC2626'
        });
    </script>
    @endif
    @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal!',
            html: '{!! implode("<br>", $errors->all()) !!}',
            confirmButtonColor: '#DC2626'
        });
    </script>
    @endif

    @stack('scripts')
</body>
</html>