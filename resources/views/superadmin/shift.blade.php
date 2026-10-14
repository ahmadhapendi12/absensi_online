@extends('layouts.superadmin')
@section('title', 'Kelola Jam Kerja & Shift')
@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-red-900">Kelola Jam Kerja & Shift</h2>
        <p class="text-sm text-gray-500">Atur jam masuk, jam pulang, dan toleransi keterlambatan shift</p>
    </div>
    <div class="flex gap-2">
        <a href="/admin/master-shifts" class="bg-blue-600 text-white font-bold py-2.5 px-4 rounded-xl hover:bg-blue-700 transition flex items-center gap-2 shadow-sm text-sm">
            <i class="fas fa-external-link-alt"></i> Buka di Panel Filament
        </a>
        <button onclick="document.getElementById('modalShift').classList.remove('hidden')" class="bg-red-600 text-white font-bold py-2.5 px-5 rounded-xl hover:bg-red-700 transition flex items-center gap-2 shadow-lg text-sm">
            <i class="fas fa-plus"></i> Tambah Shift
        </button>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl text-sm font-semibold border border-green-200">
        {{ session('success') }}
    </div>
@endif

<!-- Tabel Shift -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-red-50 text-red-900">
                <tr>
                    <th class="p-4 font-bold">Nama Shift</th>
                    <th class="p-4 font-bold">Jam Masuk</th>
                    <th class="p-4 font-bold">Jam Pulang</th>
                    <th class="p-4 font-bold">Toleransi</th>
                    <th class="p-4 font-bold">Lintas Hari</th>
                    <th class="p-4 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($shifts as $s)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-bold text-gray-800"><i class="fas fa-clock text-red-500 mr-2"></i>{{ $s->nama_shift }}</td>
                    <td class="p-4 font-mono">{{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }}</td>
                    <td class="p-4 font-mono">{{ \Carbon\Carbon::parse($s->jam_pulang)->format('H:i') }}</td>
                    <td class="p-4"><span class="bg-orange-50 text-orange-600 px-2 py-1 rounded-full text-xs font-bold">{{ $s->toleransi_terlambat_menit }} Menit</span></td>
                    <td class="p-4">
                        @if($s->lintas_hari)
                            <span class="bg-purple-50 text-purple-600 px-2 py-1 rounded-full text-xs font-bold">Ya</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-bold">Tidak</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <form id="form-hapus-shift-{{ $s->id }}" action="{{ route('superadmin.shift.destroy', $s->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="hapusShift('{{ $s->id }}', '{{ $s->nama_shift }}')" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center text-gray-400">Belum ada data shift kerja.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Shift -->
<div id="modalShift" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg text-gray-800">Tambah Shift Kerja</h3>
            <button onclick="document.getElementById('modalShift').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form action="{{ route('superadmin.shift.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Shift</label>
                <input type="text" name="nama_shift" placeholder="Contoh: Shift Pagi" required class="w-full border-gray-200 rounded-xl p-3 text-sm focus:ring-red-500 border">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jam Masuk</label>
                    <input type="time" name="jam_masuk" required class="w-full border-gray-200 rounded-xl p-3 text-sm focus:ring-red-500 border">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jam Pulang</label>
                    <input type="time" name="jam_pulang" required class="w-full border-gray-200 rounded-xl p-3 text-sm focus:ring-red-500 border">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Toleransi Terlambat (Menit)</label>
                <input type="number" name="toleransi_terlambat_menit" value="15" required class="w-full border-gray-200 rounded-xl p-3 text-sm focus:ring-red-500 border">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="lintas_hari" name="lintas_hari" value="1" class="rounded text-red-600 focus:ring-red-500">
                <label for="lintas_hari" class="text-sm text-gray-700">Shift Lintas Hari (Melewati tengah malam)</label>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalShift').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 transition">Simpan Shift</button>
            </div>
        </form>
    </div>
</div>

<script>
function hapusShift(id, nama) {
    Swal.fire({
        title: 'Hapus Shift?',
        text: "Shift " + nama + " akan dihapus permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-hapus-shift-' + id).submit();
        }
    });
}
</script>
@endsection
