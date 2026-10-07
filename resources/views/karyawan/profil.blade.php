@extends('layouts.karyawan')

@section('content')
<!-- Header -->
<div class="bg-blue-600 p-6 text-white text-center pb-24 rounded-b-[2.5rem] shadow-md">
    <h1 class="font-bold text-lg mb-6">Profil Saya</h1>
</div>

<!-- Card Profil -->
<div class="px-5 -mt-16 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
        <!-- Foto -->
        <div class="w-24 h-24 bg-blue-100 rounded-full mx-auto mb-4 border-4 border-white shadow-lg overflow-hidden flex items-center justify-center">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=EBF8FF&color=2B6CB0&size=100" alt="Profil">
        </div>
        
        <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
        <p class="text-gray-500 text-sm mb-4">{{ $user->nik }} | {{ $user->cabang->nama_cabang ?? 'Pusat' }}</p>
        
        <div class="flex justify-center gap-4 text-left border-t border-gray-100 pt-4 mt-2">
            <div class="bg-blue-50 p-3 rounded-xl w-1/2">
                <p class="text-xs text-blue-600 font-semibold mb-1">Sisa Cuti</p>
                <p class="text-lg font-bold text-gray-800">{{ $user->sisa_cuti }} <span class="text-sm font-normal">Hari</span></p>
            </div>
            <div class="bg-green-50 p-3 rounded-xl w-1/2">
                <p class="text-xs text-green-600 font-semibold mb-1">Status</p>
                <p class="text-lg font-bold text-gray-800 capitalize">{{ $user->role }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Tombol Logout -->
<div class="px-5 mt-8">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full bg-red-50 text-red-600 border border-red-200 font-bold py-3.5 rounded-xl shadow-sm hover:bg-red-100 active:scale-95 transition-all flex justify-center items-center">
            <i class="fas fa-sign-out-alt mr-2"></i> Keluar (Log Out)
        </button>
    </form>
</div>
@endsection