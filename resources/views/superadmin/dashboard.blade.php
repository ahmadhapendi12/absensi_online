@extends('layouts.superadmin')

@section('title', 'Dashboard Superadmin')

@section('content')
<!-- 4 Kartu Statistik -->
<div class="grid grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Karyawan</p>
            <h3 class="text-3xl font-bold text-[#2B3674]">124</h3>
        </div>
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-users"></i></div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Cabang</p>
            <h3 class="text-3xl font-bold text-[#2B3674]">5</h3>
        </div>
        <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-building"></i></div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Pengajuan Pending</p>
            <h3 class="text-3xl font-bold text-[#2B3674]">12</h3>
        </div>
        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-shield-alt"></i></div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Hari Ini Masuk</p>
            <h3 class="text-3xl font-bold text-[#2B3674]">118</h3>
        </div>
        <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-user-check"></i></div>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- Grafik Kehadiran (Lebar 2 Kolom) -->
    <div class="col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-[#2B3674] mb-6">Rekap Kehadiran 7 Hari Terakhir</h3>
        <canvas id="kehadiranChart" height="100"></canvas>
    </div>

    <!-- Lokasi Cabang (Lebar 1 Kolom) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-[#2B3674] mb-6">Lokasi Cabang Aktif</h3>
        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-map-marker-alt text-blue-500 mt-1"></i>
                <div>
                    <h4 class="font-bold text-sm text-gray-800">Jakarta Pusat (Pusat)</h4>
                    <p class="text-xs text-gray-500">Radius 100 m</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-map-marker-alt text-blue-500 mt-1"></i>
                <div>
                    <h4 class="font-bold text-sm text-gray-800">Bandung</h4>
                    <p class="text-xs text-gray-500">Radius 100 m</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-map-marker-alt text-blue-500 mt-1"></i>
                <div>
                    <h4 class="font-bold text-sm text-gray-800">Surabaya</h4>
                    <p class="text-xs text-gray-500">Radius 100 m</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('kehadiranChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['24 Sep', '25 Sep', '26 Sep', '27 Sep', '28 Sep', '29 Sep', '30 Sep'],
            datasets: [
                { label: 'Hadir', backgroundColor: '#3B82F6', data: [110, 115, 112, 118, 120, 119, 118] },
                { label: 'Terlambat', backgroundColor: '#F59E0B', data: [10, 5, 8, 4, 2, 3, 2] },
                { label: 'Tidak Hadir', backgroundColor: '#EF4444', data: [4, 4, 4, 2, 2, 2, 4] }
            ]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 125 } },
            plugins: { legend: { position: 'top' } }
        }
    });
</script>
@endpush