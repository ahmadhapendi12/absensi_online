@extends('layouts.karyawan')

@section('content')
<!-- Header -->
<div class="bg-white p-5 border-b border-gray-100 sticky top-0 z-40 flex items-center shadow-sm">
    <a href="{{ route('karyawan.presensi.create') }}" class="text-gray-600 text-xl mr-4"><i class="fas fa-arrow-left"></i></a>
    <h1 class="font-bold text-lg text-gray-800">Pengajuan Lembur</h1>
</div>

<div class="flex border-b border-gray-200 bg-white">
    <a href="{{ route('karyawan.pengajuan.index') }}" class="flex-1 py-3 text-center text-sm font-semibold text-gray-500 hover:text-red-600">Izin / Sakit</a>
    <a href="{{ route('karyawan.pengajuan.lembur') }}" class="flex-1 py-3 text-center text-sm font-bold text-red-600 border-b-2 border-red-600">Lembur</a>
</div>

<div class="p-5 mb-6">
    @if(session('success'))
        <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm font-semibold border border-green-200">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('karyawan.pengajuan.lembur.store') }}" method="POST" class="space-y-5">
        @csrf
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Lembur</label>
            <div class="relative">
                <input type="date" name="tanggal" required class="w-full border-gray-200 rounded-xl p-3.5 text-gray-700 focus:ring-red-500 focus:border-red-500 bg-gray-50 border">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Jam Mulai</label>
                <div class="relative">
                    <input type="time" name="jam_mulai_aktual" required class="w-full border-gray-200 rounded-xl p-3.5 text-gray-700 focus:ring-red-500 focus:border-red-500 bg-gray-50 border">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Jam Selesai</label>
                <div class="relative">
                    <input type="time" name="jam_selesai_aktual" required class="w-full border-gray-200 rounded-xl p-3.5 text-gray-700 focus:ring-red-500 focus:border-red-500 bg-gray-50 border">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Lembur</label>
            <textarea name="alasan_lembur" rows="4" placeholder="Alasan mengapa lembur..." required class="w-full border-gray-200 rounded-xl p-3.5 text-gray-700 focus:ring-red-500 focus:border-red-500 bg-gray-50 border resize-none"></textarea>
        </div>

        <button type="submit" class="w-full bg-red-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-red-700 active:scale-95 transition-all mt-4">
            Kirim Pengajuan Lembur
        </button>
    </form>
</div>
@endsection
