@extends('layouts.karyawan')

@section('content')
<!-- Header Sticky -->
<div class="bg-white p-5 border-b border-gray-100 sticky top-0 z-40 flex items-center justify-between shadow-sm">
    <div class="flex items-center">
        <a href="{{ route('karyawan.presensi.create') }}" class="text-gray-600 text-xl mr-3 hover:text-red-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="font-bold text-lg text-gray-800">Riwayat Presensi</h1>
    </div>

    <!-- Tombol Unduh Laporan PDF Modal Trigger -->
    <button type="button" onclick="openPdfModal()" class="inline-flex items-center space-x-1.5 bg-gradient-to-r from-red-600 to-rose-600 text-white text-xs font-bold px-3 py-2 rounded-xl shadow-md hover:from-red-700 hover:to-rose-700 active:scale-95 transition-all">
        <i class="fas fa-file-pdf text-sm"></i>
        <span>Unduh PDF</span>
    </button>
</div>

<div class="p-5 mb-8">
    <!-- Filter Bar Bulan & Tahun -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
        <form method="GET" action="{{ route('karyawan.riwayat.index') }}" id="filterForm" class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                    <i class="far fa-calendar-alt text-red-600 mr-1.5"></i> Periode Presensi
                </span>
                <span class="text-[11px] font-semibold text-gray-400">Total: {{ $riwayat->count() }} Hari</span>
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <select name="bulan" onchange="document.getElementById('filterForm').submit()" class="border-gray-200 rounded-xl p-2.5 text-xs font-bold text-gray-800 bg-gray-50 focus:ring-red-500 focus:border-red-500 border">
                    @foreach([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ] as $num => $namaBulan)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                    @endforeach
                </select>

                <select name="tahun" onchange="document.getElementById('filterForm').submit()" class="border-gray-200 rounded-xl p-2.5 text-xs font-bold text-gray-800 bg-gray-50 focus:ring-red-500 focus:border-red-500 border">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Filter Status Kancing / Pills -->
            <div class="flex items-center space-x-1.5 overflow-x-auto pt-1 pb-0.5 no-scrollbar">
                @php
                    $statusList = [
                        'semua' => 'Semua',
                        'Tepat Waktu' => 'Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Cuti' => 'Cuti',
                    ];
                @endphp
                @foreach($statusList as $key => $lbl)
                    <button type="submit" name="status" value="{{ $key }}" class="px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap transition-all {{ ($status == $key || (!$status && $key == 'semua')) ? 'bg-red-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $lbl }}
                    </button>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Timeline Riwayat Presensi -->
    <div class="space-y-5">
        @forelse($riwayat as $data)
        @php
            $isTepat = in_array($data->status_masuk, ['Tepat Waktu', 'Hadir']);
            $isTerlambat = $data->status_masuk == 'Terlambat';
            $isIzin = in_array($data->status_masuk, ['Izin', 'Sakit', 'Cuti']);
            
            $dotColor = $isTepat ? 'bg-emerald-500' : ($isTerlambat ? 'bg-amber-500' : 'bg-indigo-500');
            $badgeBg = $isTepat ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isTerlambat ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200');
        @endphp
        <div class="relative pl-5 border-l-2 border-gray-200 ml-2">
            <!-- Dot Marker -->
            <div class="absolute w-3.5 h-3.5 rounded-full {{ $dotColor }} -left-[8px] top-1 border-2 border-white shadow-xs"></div>
            
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-bold text-gray-800 text-xs">
                    {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('l, d F Y') }}
                </h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $badgeBg }}">
                    {{ $data->status_masuk ?? 'Hadir' }}
                </span>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 space-y-3 hover:border-gray-200 transition">
                <!-- Info Shift -->
                @if($data->jadwal?->shift)
                <div class="flex items-center justify-between text-[11px] text-gray-500 pb-2 border-b border-gray-100">
                    <span class="font-semibold text-gray-700"><i class="fas fa-business-time mr-1 text-red-500"></i> {{ $data->jadwal->shift->nama_shift }}</span>
                    <span>{{ \Carbon\Carbon::parse($data->jadwal->shift->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($data->jadwal->shift->jam_pulang)->format('H:i') }}</span>
                </div>
                @endif

                <!-- Data Masuk -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-700 block">Jam Masuk</span>
                            <span class="text-[11px] text-gray-400">
                                @if($data->menit_terlambat > 0)
                                    <span class="text-amber-600 font-semibold">+{{ $data->menit_terlambat }} mnt terlambat</span>
                                @else
                                    Tepat Waktu
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-black text-gray-900 text-base font-mono">
                            {{ $data->jam_masuk ? \Carbon\Carbon::parse($data->jam_masuk)->format('H:i') : '-' }}
                        </span>
                    </div>
                </div>
                
                <hr class="border-gray-100">
                
                <!-- Data Pulang -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-700 block">Jam Pulang</span>
                            <span class="text-[11px] text-gray-400">{{ $data->jam_pulang ? 'Presensi Selesai' : 'Belum Presensi Pulang' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-black text-gray-900 text-base font-mono">
                            {{ $data->jam_pulang ? \Carbon\Carbon::parse($data->jam_pulang)->format('H:i') : '-' }}
                        </span>
                    </div>
                </div>

                @if($data->keterangan)
                <div class="pt-2 text-[11px] text-gray-500 italic bg-gray-50 p-2 rounded-lg border border-gray-100">
                    <i class="fas fa-info-circle mr-1 text-gray-400"></i> {{ $data->keterangan }}
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center text-gray-400 py-12 bg-white rounded-2xl border border-gray-100 p-6">
            <div class="bg-red-50 text-red-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                <i class="fas fa-calendar-times text-2xl"></i>
            </div>
            <h4 class="font-bold text-gray-700 text-sm mb-1">Belum Ada Presensi</h4>
            <p class="text-xs text-gray-500 max-w-xs mx-auto">Tidak ditemukan catatan presensi pada periode bulan yang dipilih.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL UNDUH LAPORAN PDF DENGAN PENGATURAN LENGKAP             -->
<!-- ============================================================== -->
<div id="pdfModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl border border-gray-100 overflow-hidden animate-fade-in" onclick="event.stopPropagation()">
        <!-- Header Modal -->
        <div class="bg-gradient-to-r from-red-600 to-rose-600 p-4 text-white text-center relative">
            <button type="button" onclick="closePdfModal()" class="absolute right-3.5 top-3.5 text-white/80 hover:text-white text-lg">
                <i class="fas fa-times"></i>
            </button>
            <div class="w-10 h-10 rounded-full bg-white/20 mx-auto flex items-center justify-center mb-1 shadow-inner">
                <i class="fas fa-file-pdf text-xl"></i>
            </div>
            <h3 class="font-black text-base">Unduh Rekapitulasi Presensi</h3>
            <p class="text-[11px] text-red-100">Download laporan resmi presensi dalam format PDF berkualitas tinggi</p>
        </div>

        <!-- Form Pengaturan Download PDF -->
        <form action="{{ route('laporan.export-pdf') }}" method="GET" target="_blank" class="p-5 space-y-4">
            <!-- Pilihan Periode -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Pilihan Periode Laporan</label>
                <select name="tipe_periode" id="tipe_periode_pdf" onchange="togglePdfPeriodeInputs()" class="w-full border-gray-200 rounded-xl p-3 text-xs font-bold text-gray-800 bg-gray-50 focus:ring-red-500 focus:border-red-500 border">
                    <option value="bulan" selected>Bulan Tertentu</option>
                    <option value="tahun">1 Tahun Penuh</option>
                    <option value="hari_ini">Hari Ini Saja</option>
                    <option value="rentang">Rentang Tanggal Kustom</option>
                </select>
            </div>

            <!-- Field Bulan & Tahun -->
            <div id="wrapper_bulan_tahun" class="grid grid-cols-2 gap-3">
                <div id="wrapper_bulan">
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Bulan</label>
                    <select name="bulan" class="w-full border-gray-200 rounded-xl p-2.5 text-xs font-bold text-gray-800 bg-gray-50 focus:ring-red-500 focus:border-red-500 border">
                        @foreach([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ] as $num => $namaBulan)
                            <option value="{{ $num }}" {{ now()->month == $num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Tahun</label>
                    <select name="tahun" class="w-full border-gray-200 rounded-xl p-2.5 text-xs font-bold text-gray-800 bg-gray-50 focus:ring-red-500 focus:border-red-500 border">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Field Rentang Tanggal (Kustom) -->
            <div id="wrapper_rentang" class="grid grid-cols-2 gap-3 hidden">
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ now()->startOfMonth()->toDateString() }}" class="w-full border-gray-200 rounded-xl p-2.5 text-xs font-semibold text-gray-800 bg-gray-50 focus:ring-red-500 focus:border-red-500 border">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ now()->toDateString() }}" class="w-full border-gray-200 rounded-xl p-2.5 text-xs font-semibold text-gray-800 bg-gray-50 focus:ring-red-500 focus:border-red-500 border">
                </div>
            </div>

            <!-- Filter Status Kehadiran -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Kriteria Status Presensi</label>
                <select name="status_masuk" class="w-full border-gray-200 rounded-xl p-3 text-xs font-bold text-gray-800 bg-gray-50 focus:ring-red-500 focus:border-red-500 border">
                    <option value="semua">Semua Status (Lengkap)</option>
                    <option value="Tepat Waktu">Tepat Waktu Saja</option>
                    <option value="Terlambat">Terlambat Saja</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Cuti">Cuti</option>
                </select>
            </div>

            <!-- Catatan Branding -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 flex items-start space-x-2 text-[11px] text-gray-600">
                <i class="fas fa-check-circle text-red-600 mt-0.5"></i>
                <span>File PDF dilengkapi kop resmi PT Lazatto Sinergi Sukses, statistik kehadiran, rincian per hari, dan lembar pengesahan.</span>
            </div>

            <!-- Tombol Aksi Modal -->
            <div class="pt-2 flex items-center justify-end space-x-2">
                <button type="button" onclick="closePdfModal()" class="px-4 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-700">
                    Batal
                </button>
                <button type="submit" onclick="setTimeout(closePdfModal, 800)" class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-xs font-bold rounded-xl shadow-md transition active:scale-95 flex items-center space-x-2">
                    <i class="fas fa-download"></i>
                    <span>Generate & Unduh PDF</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPdfModal() {
        document.getElementById('pdfModal').classList.remove('hidden');
    }

    function closePdfModal() {
        document.getElementById('pdfModal').classList.add('hidden');
    }

    function togglePdfPeriodeInputs() {
        const tipe = document.getElementById('tipe_periode_pdf').value;
        const wrapperBulanTahun = document.getElementById('wrapper_bulan_tahun');
        const wrapperBulan = document.getElementById('wrapper_bulan');
        const wrapperRentang = document.getElementById('wrapper_rentang');

        if (tipe === 'bulan') {
            wrapperBulanTahun.classList.remove('hidden');
            wrapperBulan.classList.remove('hidden');
            wrapperRentang.classList.add('hidden');
        } else if (tipe === 'tahun') {
            wrapperBulanTahun.classList.remove('hidden');
            wrapperBulan.classList.add('hidden');
            wrapperRentang.classList.add('hidden');
        } else if (tipe === 'rentang') {
            wrapperBulanTahun.classList.add('hidden');
            wrapperRentang.classList.remove('hidden');
        } else if (tipe === 'hari_ini') {
            wrapperBulanTahun.classList.add('hidden');
            wrapperRentang.classList.add('hidden');
        }
    }
</script>
@endsection