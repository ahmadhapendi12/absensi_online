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

<div class="p-5 mb-8">
    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 text-sm font-semibold border border-green-200 flex items-center shadow-sm">
            <i class="fas fa-check-circle text-green-600 text-lg mr-2.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Kartu Informasi Karyawan & Cabang (Posisi Pertama) -->
    <div class="bg-gradient-to-r from-red-600 to-rose-700 rounded-2xl p-4 text-white shadow-md mb-6 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 text-white/10 text-8xl pointer-events-none">
            <i class="fas fa-clock"></i>
        </div>
        <div class="relative z-10 flex items-center space-x-3.5">
            <img src="{{ auth()->user()->avatar_url }}" alt="Profile" class="w-12 h-12 rounded-full border-2 border-white/80 object-cover shadow-sm bg-white">
            <div class="flex-1 min-w-0">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-red-100 block">Karyawan Pemohon</span>
                <h3 class="font-bold text-base truncate">{{ auth()->user()->name }}</h3>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="inline-flex items-center text-[10px] font-bold bg-white/20 text-white px-2 py-0.5 rounded-full backdrop-blur-sm">
                        <i class="fas fa-store mr-1 text-[9px]"></i> {{ auth()->user()->cabang?->nama_cabang ?? 'Pusat / Seluruh Outlet' }}
                    </span>
                    <span class="text-[10px] text-red-100">
                        NIK: {{ auth()->user()->profile?->nik ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('karyawan.pengajuan.lembur.store') }}" method="POST" id="formLembur" class="space-y-5">
        @csrf
        
        <!-- Tanggal Lembur -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Tanggal Lembur</label>
            <div class="relative">
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full border-gray-200 rounded-xl p-3.5 text-gray-800 font-semibold focus:ring-red-500 focus:border-red-500 bg-gray-50 border">
            </div>
        </div>

        <!-- Pemilih Jam Lembur (Format 24 Jam Indonesia: Jam & Menit Dropdown, Tanpa AM/PM) -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Jam Lembur (Format 24 Jam WIB)</label>
            
            <div class="grid grid-cols-2 gap-3">
                <!-- Jam Mulai -->
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-3 focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-200 transition">
                    <label class="block text-[11px] font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                        <span><i class="far fa-clock text-red-500 mr-1"></i> Jam Mulai</span>
                        <span class="text-[10px] text-gray-400 font-normal">24 Jam</span>
                    </label>
                    <div class="flex items-center space-x-1.5">
                        <select id="sel_jam_mulai" onchange="syncFromSelects()" class="flex-1 bg-white border border-gray-200 rounded-xl p-2 text-sm font-black text-gray-900 focus:outline-none focus:border-red-500">
                            @for($h = 0; $h < 24; $h++)
                                @php $hh = sprintf('%02d', $h); @endphp
                                <option value="{{ $hh }}" {{ $hh == '17' ? 'selected' : '' }}>{{ $hh }}:00</option>
                            @endfor
                        </select>
                        <select id="sel_menit_mulai" onchange="syncFromSelects()" class="w-16 bg-white border border-gray-200 rounded-xl p-2 text-sm font-black text-gray-900 focus:outline-none focus:border-red-500">
                            <option value="00" selected>:00</option>
                            <option value="15">:15</option>
                            <option value="30">:30</option>
                            <option value="45">:45</option>
                        </select>
                    </div>
                </div>

                <!-- Jam Selesai -->
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-3 focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-200 transition">
                    <label class="block text-[11px] font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                        <span><i class="far fa-clock text-red-500 mr-1"></i> Jam Selesai</span>
                        <span class="text-[10px] text-gray-400 font-normal">24 Jam</span>
                    </label>
                    <div class="flex items-center space-x-1.5">
                        <select id="sel_jam_selesai" onchange="syncFromSelects()" class="flex-1 bg-white border border-gray-200 rounded-xl p-2 text-sm font-black text-gray-900 focus:outline-none focus:border-red-500">
                            @for($h = 0; $h < 24; $h++)
                                @php $hh = sprintf('%02d', $h); @endphp
                                <option value="{{ $hh }}" {{ $hh == '19' ? 'selected' : '' }}>{{ $hh }}:00</option>
                            @endfor
                        </select>
                        <select id="sel_menit_selesai" onchange="syncFromSelects()" class="w-16 bg-white border border-gray-200 rounded-xl p-2 text-sm font-black text-gray-900 focus:outline-none focus:border-red-500">
                            <option value="00" selected>:00</option>
                            <option value="15">:15</option>
                            <option value="30">:30</option>
                            <option value="45">:45</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Hidden Inputs yang dikirim ke controller -->
            <input type="hidden" name="jam_mulai_aktual" id="jam_mulai_input" value="17:00">
            <input type="hidden" name="jam_selesai_aktual" id="jam_selesai_input" value="19:00">

            <!-- Tombol Cepat Pilihan Durasi -->
            <div class="flex items-center space-x-2 mt-2.5 overflow-x-auto pb-1 no-scrollbar">
                <span class="text-[10px] font-bold text-gray-400 whitespace-nowrap">Pilihan Cepat:</span>
                <button type="button" onclick="setPresetTime('17', '00', '18', '00')" class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 rounded-lg text-gray-700 hover:border-red-500 hover:text-red-600 transition whitespace-nowrap shadow-2xs">
                    1 Jam (17-18)
                </button>
                <button type="button" onclick="setPresetTime('17', '00', '19', '00')" class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 rounded-lg text-gray-700 hover:border-red-500 hover:text-red-600 transition whitespace-nowrap shadow-2xs">
                    2 Jam (17-19)
                </button>
                <button type="button" onclick="setPresetTime('17', '00', '20', '00')" class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 rounded-lg text-gray-700 hover:border-red-500 hover:text-red-600 transition whitespace-nowrap shadow-2xs">
                    3 Jam (17-20)
                </button>
                <button type="button" onclick="setPresetTime('22', '00', '02', '00')" class="px-2.5 py-1 text-[11px] font-semibold bg-white border border-gray-200 rounded-lg text-gray-700 hover:border-red-500 hover:text-red-600 transition whitespace-nowrap shadow-2xs">
                    Lintas Hari (22-02)
                </button>
            </div>

            <!-- Sinkronisasi Durasi Lembur Real-Time -->
            <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-amber-800">
                    <i class="fas fa-hourglass-half text-amber-600"></i>
                    <span class="text-xs font-semibold">Total Durasi Lembur:</span>
                </div>
                <span class="text-xs font-black text-amber-900 bg-amber-100 px-2.5 py-1 rounded-lg" id="text_durasi_lembur">
                    2 Jam 0 Menit (120 Menit)
                </span>
            </div>
        </div>

        <!-- Alasan Lembur -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Alasan / Uraian Tugas Lembur</label>
            <textarea name="alasan_lembur" rows="3" placeholder="Tuliskan pekerjaan atau keperluan lembur kios hari ini..." required class="w-full border-gray-200 rounded-xl p-3.5 text-gray-800 text-sm focus:ring-red-500 focus:border-red-500 bg-gray-50 border resize-none"></textarea>
        </div>

        <button type="submit" class="w-full bg-red-600 text-white font-bold py-3.5 rounded-xl shadow-lg hover:bg-red-700 active:scale-95 transition-all flex items-center justify-center space-x-2">
            <i class="fas fa-paper-plane"></i>
            <span>Kirim Pengajuan Lembur</span>
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        syncFromSelects();
    });

    function syncFromSelects() {
        const jMulai = document.getElementById('sel_jam_mulai').value;
        const mMulai = document.getElementById('sel_menit_mulai').value;
        const jSelesai = document.getElementById('sel_jam_selesai').value;
        const mSelesai = document.getElementById('sel_menit_selesai').value;

        const mulai = `${jMulai}:${mMulai}`;
        const selesai = `${jSelesai}:${mSelesai}`;

        document.getElementById('jam_mulai_input').value = mulai;
        document.getElementById('jam_selesai_input').value = selesai;

        let minMulai = parseInt(jMulai, 10) * 60 + parseInt(mMulai, 10);
        let minSelesai = parseInt(jSelesai, 10) * 60 + parseInt(mSelesai, 10);

        let isOvernight = false;
        if (minSelesai < minMulai) {
            minSelesai += 24 * 60;
            isOvernight = true;
        }

        let durasi = minSelesai - minMulai;
        let hours = Math.floor(durasi / 60);
        let mins = durasi % 60;

        let label = `${hours} Jam ${mins} Menit (${durasi} Menit)`;
        if (isOvernight) {
            label += ' - Lintas Hari';
        }

        document.getElementById('text_durasi_lembur').innerText = label;
    }

    function setPresetTime(jM, mM, jS, mS) {
        document.getElementById('sel_jam_mulai').value = jM;
        document.getElementById('sel_menit_mulai').value = mM;
        document.getElementById('sel_jam_selesai').value = jS;
        document.getElementById('sel_menit_selesai').value = mS;
        syncFromSelects();
    }
</script>
@endsection
