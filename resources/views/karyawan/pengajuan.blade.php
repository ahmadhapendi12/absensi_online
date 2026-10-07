@extends('layouts.karyawan')

@section('content')
<!-- Header -->
<div class="bg-white p-5 border-b border-gray-100 sticky top-0 z-40 flex items-center shadow-sm">
    <a href="{{ route('karyawan.presensi.create') }}" class="text-gray-600 text-xl mr-4"><i class="fas fa-arrow-left"></i></a>
    <h1 class="font-bold text-lg text-gray-800">Izin / Sakit / Cuti</h1>
</div>

<div class="p-5 mb-6">
    @if(session('success'))
        <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm font-semibold border border-green-200">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('karyawan.pengajuan.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        
        <!-- Jenis Izin -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Izin</label>
            <div class="relative">
                <select name="jenis" required class="w-full border-gray-200 text-gray-700 rounded-xl p-3.5 appearance-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50 border">
                    <option value="" disabled selected>Pilih jenis izin...</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Cuti">Cuti</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-4 text-gray-400"></i>
            </div>
        </div>

        <!-- Tanggal -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Mulai</label>
                <div class="relative">
                    <input type="date" name="tanggal_mulai" required class="w-full border-gray-200 rounded-xl p-3.5 text-gray-700 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 border">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Selesai</label>
                <div class="relative">
                    <input type="date" name="tanggal_selesai" required class="w-full border-gray-200 rounded-xl p-3.5 text-gray-700 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 border">
                </div>
            </div>
        </div>

        <!-- Keterangan -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan</label>
            <textarea name="alasan" rows="4" placeholder="Alasan pengajuan izin..." required class="w-full border-gray-200 rounded-xl p-3.5 text-gray-700 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 border resize-none"></textarea>
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 active:scale-95 transition-all mt-4">
            Kirim Pengajuan
        </button>
    </form>
</div>
@endsection