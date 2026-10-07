@extends('layouts.karyawan')

@section('content')
<!-- Header -->
<div class="bg-white p-5 border-b border-gray-100 sticky top-0 z-40 flex items-center shadow-sm">
    <a href="{{ route('karyawan.presensi.create') }}" class="text-gray-600 text-xl mr-4"><i class="fas fa-arrow-left"></i></a>
    <h1 class="font-bold text-lg text-gray-800">Riwayat Presensi</h1>
</div>

<div class="p-5 mb-6">
    <!-- Filter Bulan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-8 flex justify-between items-center cursor-pointer">
        <span class="font-bold text-gray-800">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
        <i class="far fa-calendar-alt text-blue-600 text-lg"></i>
    </div>

    <!-- Timeline Riwayat -->
    <div class="space-y-8">
        @forelse($riwayat as $data)
        <div class="relative pl-6 border-l-2 border-gray-200">
            <!-- Dot Marker -->
            <div class="absolute w-4 h-4 rounded-full bg-blue-600 -left-[9px] top-0 border-4 border-white shadow-sm"></div>
            
            <h3 class="font-bold text-gray-800 text-sm mb-4">
                {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('l, d F Y') }}
            </h3>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <!-- Data Masuk -->
                <div class="flex justify-between items-start">
                    <div class="flex items-start gap-3">
                        <div class="w-2.5 h-2.5 rounded-full mt-1.5 {{ $data->status_masuk == 'Tepat Waktu' ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                        <div>
                            <span class="text-sm font-semibold text-gray-700 block">Masuk</span>
                            @if($data->foto_masuk)
                                <img src="{{ asset('storage/' . $data->foto_masuk) }}" class="w-12 h-16 object-cover rounded-md border border-gray-200 mt-2 shadow-sm" alt="Foto Masuk">
                            @endif
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <span class="text-[11px] font-medium px-2 py-1 rounded-md mb-1 {{ $data->status_masuk == 'Tepat Waktu' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                            {{ $data->status_masuk }}
                        </span>
                        <span class="font-bold text-gray-900 text-lg">{{ $data->jam_masuk ? \Carbon\Carbon::parse($data->jam_masuk)->format('H:i') : '-' }}</span>
                    </div>
                </div>
                
                <hr class="border-gray-50">
                
                <!-- Data Pulang -->
                <div class="flex justify-between items-start">
                    <div class="flex items-start gap-3">
                        <div class="w-2.5 h-2.5 rounded-full mt-1.5 bg-blue-500"></div>
                        <div>
                            <span class="text-sm font-semibold text-gray-700 block">Pulang</span>
                            @if($data->foto_pulang)
                                <img src="{{ asset('storage/' . $data->foto_pulang) }}" class="w-12 h-16 object-cover rounded-md border border-gray-200 mt-2 shadow-sm" alt="Foto Pulang">
                            @endif
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <span class="text-[11px] font-medium px-2 py-1 rounded-md mb-1 bg-blue-50 text-blue-600">
                            {{ $data->jam_pulang ? 'Selesai' : 'Belum Absen' }}
                        </span>
                        <span class="font-bold text-gray-900 text-lg">{{ $data->jam_pulang ? \Carbon\Carbon::parse($data->jam_pulang)->format('H:i') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-400 py-10">
            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-folder-open text-2xl"></i>
            </div>
            <p class="text-sm">Belum ada riwayat presensi bulan ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection