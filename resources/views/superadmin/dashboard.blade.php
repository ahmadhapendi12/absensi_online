@extends('layouts.superadmin')

@section('title', 'Dashboard Superadmin')

@section('content')

<!-- Header & Jam Real-Time -->
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold text-red-900">Selamat Datang, {{ Auth::user()->name ?? 'Superadmin' }}</h2>
        <p id="realtime-clock" class="text-gray-500 text-sm mt-1">Memuat waktu...</p>
    </div>
</div>

<!-- 4 Kartu Statistik -->
<div class="grid grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Karyawan</p>
            <h3 class="text-3xl font-bold text-red-900">{{ $totalKaryawan ?? 0 }}</h3>
        </div>
        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-users"></i></div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Cabang</p>
            <h3 class="text-3xl font-bold text-red-900">{{ $totalCabang ?? 0 }}</h3>
        </div>
        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-building"></i></div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Pengajuan Pending</p>
            <h3 class="text-3xl font-bold text-red-900">{{ $pengajuanPending ?? 0 }}</h3>
        </div>
        <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-shield-alt"></i></div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Hari Ini Masuk</p>
            <h3 class="text-3xl font-bold text-red-900">{{ $hadirHariIni ?? 0 }}</h3>
        </div>
        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-user-check"></i></div>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- Grafik Kehadiran (Lebar 2 Kolom) -->
    <div class="col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-red-900 mb-6">Rekap Kehadiran 7 Hari Terakhir</h3>
        <!-- Bungkus canvas dengan div berukuran pasti agar chart tidak bocor -->
        <div class="relative w-full h-72">
            <canvas id="kehadiranChart"></canvas>
        </div>
    </div>

    <!-- Lokasi Cabang (Lebar 1 Kolom) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-red-900 mb-6">Lokasi Cabang Aktif</h3>
        <div class="space-y-4 overflow-y-auto max-h-72 pr-2">
            
            <!-- Looping data cabang dari database -->
            @forelse($listCabang ?? [] as $cabang)
            <div class="flex items-start gap-3 border-b border-gray-50 pb-3 last:border-0">
                <i class="fas fa-map-marker-alt text-red-600 mt-1"></i>
                <div>
                    <h4 class="font-bold text-sm text-gray-800">{{ $cabang->nama_cabang }}</h4>
                    <p class="text-xs text-gray-500">Radius {{ $cabang->radius ?? 100 }} m</p>
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <p class="text-sm text-gray-500">Belum ada data cabang terdaftar.</p>
            </div>
            @endforelse

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Script Jam Real-Time
    function updateClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        document.getElementById('realtime-clock').textContent = now.toLocaleDateString('id-ID', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Script Chart.js (Grafik)
    const ctx = document.getElementById('kehadiranChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            // Nanti labels dan data ini bisa diubah menjadi dinamis dari Controller dengan json_encode
            labels: {!! json_encode($chartLabels ?? ['24 Sep', '25 Sep', '26 Sep', '27 Sep', '28 Sep', '29 Sep', '30 Sep']) !!},
            datasets: [
                { label: 'Hadir', backgroundColor: '#DC2626', data: {!! json_encode($chartHadir ?? [110, 115, 112, 118, 120, 119, 118]) !!} },
                { label: 'Terlambat', backgroundColor: '#F59E0B', data: {!! json_encode($chartTerlambat ?? [10, 5, 8, 4, 2, 3, 2]) !!} },
                { label: 'Tidak Hadir', backgroundColor: '#4B5563', data: {!! json_encode($chartAbsen ?? [4, 4, 4, 2, 2, 2, 4]) !!} }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Wajib agar grafik tidak keluar batas
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: 'top' } }
        }
    });
</script>
@endpush