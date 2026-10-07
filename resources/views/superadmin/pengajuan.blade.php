@extends('layouts.superadmin')
@section('title', 'Validasi Pengajuan')
@section('content')

<div class="mb-6">
    <h2 class="text-xl font-bold text-red-900">Validasi Pengajuan</h2>
    <p class="text-sm text-gray-500">Setujui atau tolak pengajuan izin dan lembur karyawan</p>
</div>

<!-- Antrean Izin/Cuti/Sakit -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-4 bg-red-50 border-b border-gray-100 font-bold text-red-900"><i class="fas fa-file-medical mr-2"></i> Antrean Izin, Sakit & Cuti</div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-gray-500 bg-gray-50">
                <tr>
                    <th class="p-4 font-bold">Karyawan</th>
                    <th class="p-4 font-bold">Jenis</th>
                    <th class="p-4 font-bold">Tanggal</th>
                    <th class="p-4 font-bold">Alasan</th>
                    <th class="p-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($izins as $izin)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-bold text-gray-800">{{ $izin->user->name }}</td>
                    <td class="p-4"><span class="bg-yellow-50 text-yellow-600 px-2 py-1 rounded-full text-xs font-bold">{{ $izin->jenis }}</span></td>
                    <td class="p-4">{{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M') }}</td>
                    <td class="p-4 text-xs text-gray-500 max-w-xs">{{ $izin->alasan }}</td>
                    <td class="p-4 text-right">
                        <form id="form-izin-{{ $izin->id }}" action="{{ route('superadmin.pengajuan.izin.approve', $izin->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="button" onclick="konfirmasiAksi('form-izin-{{ $izin->id }}', 'Setujui Izin?', 'Izin dari {{ $izin->user->name }} akan disetujui.')" class="bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-yellow-600 transition"><i class="fas fa-check mr-1"></i>Setujui</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-4 text-center text-gray-400">Tidak ada pengajuan izin pending.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Antrean Lembur -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-4 bg-orange-50 border-b border-gray-100 font-bold text-red-900"><i class="fas fa-clock mr-2"></i> Antrean Pengajuan Lembur</div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-gray-500 bg-gray-50">
                <tr>
                    <th class="p-4 font-bold">Karyawan</th>
                    <th class="p-4 font-bold">Tanggal</th>
                    <th class="p-4 font-bold">Waktu (Jam)</th>
                    <th class="p-4 font-bold">Alasan</th>
                    <th class="p-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($lemburs as $lembur)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-bold text-gray-800">{{ $lembur->user->name }}</td>
                    <td class="p-4">{{ \Carbon\Carbon::parse($lembur->tanggal)->translatedFormat('d M Y') }}</td>
                    <td class="p-4">
                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-bold">
                            {{ \Carbon\Carbon::parse($lembur->jam_mulai_aktual)->format('H:i') }} - {{ \Carbon\Carbon::parse($lembur->jam_selesai_aktual)->format('H:i') }}
                        </span>
                    </td>
                    <td class="p-4 text-xs text-gray-500 max-w-xs">{{ $lembur->alasan_lembur }}</td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2">
                            <form id="form-lembur-acc-{{ $lembur->id }}" action="{{ route('superadmin.pengajuan.lembur.approve', $lembur->id) }}" method="POST">
                                @csrf
                                <button type="button" onclick="konfirmasiAksi('form-lembur-acc-{{ $lembur->id }}', 'Setujui Lembur?', 'Lembur dari {{ $lembur->user->name }} akan disetujui.')" class="bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-yellow-600 transition"><i class="fas fa-check mr-1"></i>Setuju</button>
                            </form>
                            <form id="form-lembur-rej-{{ $lembur->id }}" action="{{ route('superadmin.pengajuan.lembur.reject', $lembur->id) }}" method="POST">
                                @csrf
                                <button type="button" onclick="konfirmasiTolak('form-lembur-rej-{{ $lembur->id }}', '{{ $lembur->user->name }}')" class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-600 transition"><i class="fas fa-times mr-1"></i>Tolak</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-4 text-center text-gray-400">Tidak ada pengajuan lembur pending.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function konfirmasiAksi(formId, judul, teks) {
    Swal.fire({
        title: judul,
        text: teks,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#F59E0B',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

function konfirmasiTolak(formId, nama) {
    Swal.fire({
        title: 'Tolak Pengajuan?',
        html: 'Pengajuan lembur dari <b>' + nama + '</b> akan ditolak.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
</script>
@endpush