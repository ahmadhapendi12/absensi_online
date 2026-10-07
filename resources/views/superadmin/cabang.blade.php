@extends('layouts.superadmin')
@section('title', 'Kelola Cabang & GPS')
@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-red-900">Lokasi Cabang & Titik GPS</h2>
        <p class="text-sm text-gray-500">Kelola radius geofencing setiap cabang</p>
    </div>
    <button onclick="document.getElementById('modalCabang').classList.remove('hidden')" class="bg-red-600 text-white font-bold py-2.5 px-5 rounded-xl hover:bg-red-700 transition flex items-center gap-2 shadow-lg">
        <i class="fas fa-plus"></i> Tambah Cabang
    </button>
</div>

<!-- Tabel Cabang -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-red-50 text-red-900">
                <tr>
                    <th class="p-4 font-bold">Nama Cabang</th>
                    <th class="p-4 font-bold">Titik Koordinat (Lat, Lng)</th>
                    <th class="p-4 font-bold">Batas Radius</th>
                    <th class="p-4 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($cabangs as $c)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-bold text-gray-800"><i class="fas fa-map-marker-alt text-red-500 mr-2"></i>{{ $c->nama_cabang }}</td>
                    <td class="p-4 font-mono text-xs text-gray-500">{{ $c->latitude }}, {{ $c->longitude }}</td>
                    <td class="p-4"><span class="bg-green-50 text-green-600 px-2 py-1 rounded-full text-xs font-bold">{{ $c->radius_meter }} Meter</span></td>
                    <td class="p-4 text-center">
                        <form id="form-hapus-cabang-{{ $c->id }}" action="{{ route('superadmin.cabang.destroy', $c->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" onclick="hapusCabang('{{ $c->id }}', '{{ $c->nama_cabang }}')" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition"><i class="fas fa-trash mr-1"></i>Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-6 text-center text-gray-400">Belum ada data cabang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH CABANG -->
<div id="modalCabang" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-red-600 rounded-t-2xl">
            <h3 class="text-lg font-bold text-white"><i class="fas fa-map-marker-alt mr-2"></i> Tambah Titik Cabang Baru</h3>
            <button onclick="document.getElementById('modalCabang').classList.add('hidden')" class="text-white/80 hover:text-white text-xl"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('superadmin.cabang.store') }}" method="POST" class="p-6 space-y-4" id="formCabang">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Nama Cabang</label>
                <input type="text" name="nama_cabang" placeholder="Misal: Bandung" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Latitude</label>
                    <input type="text" name="latitude" placeholder="-6.xxxxx" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Longitude</label>
                    <input type="text" name="longitude" placeholder="106.xxxxx" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Radius (Meter)</label>
                <input type="number" name="radius_meter" value="100" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalCabang').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 font-bold py-2.5 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button type="submit" class="flex-1 bg-red-600 text-white font-bold py-2.5 rounded-lg hover:bg-red-700 transition">Simpan Cabang</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function hapusCabang(id, nama) {
    Swal.fire({
        title: 'Hapus Cabang?',
        html: 'Cabang <b>' + nama + '</b> akan dihapus permanen beserta relasi karyawannya!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-hapus-cabang-' + id).submit();
        }
    });
}

document.getElementById('formCabang').addEventListener('submit', function(e) {
    e.preventDefault();
    let form = this;
    Swal.fire({
        title: 'Simpan Cabang Baru?',
        text: 'Pastikan koordinat GPS sudah benar.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
@endpush