@extends('layouts.superadmin')
@section('title', 'Kelola Karyawan')
@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-red-900">Data Karyawan</h2>
        <p class="text-sm text-gray-500">Kelola semua data karyawan Lazatto</p>
    </div>
    <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-red-600 text-white font-bold py-2.5 px-5 rounded-xl hover:bg-red-700 transition flex items-center gap-2 shadow-lg">
        <i class="fas fa-plus"></i> Tambah Karyawan
    </button>
</div>

<!-- Tabel Karyawan -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-red-50 text-red-900">
                <tr>
                    <th class="p-4 font-bold">NIK</th>
                    <th class="p-4 font-bold">Nama Karyawan</th>
                    <th class="p-4 font-bold">No. HP</th>
                    <th class="p-4 font-bold">Cabang</th>
                    <th class="p-4 font-bold">Sisa Cuti</th>
                    <th class="p-4 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($karyawans as $k)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-mono text-sm">{{ $k->nik }}</td>
                    <td class="p-4">
                        <p class="font-bold text-gray-800">{{ $k->name }}</p>
                        <p class="text-xs text-gray-400">{{ $k->email }}</p>
                    </td>
                    <td class="p-4 text-gray-600">{{ $k->no_hp ?? '-' }}</td>
                    <td class="p-4"><span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-bold">{{ $k->cabang->nama_cabang ?? 'Pusat' }}</span></td>
                    <td class="p-4 font-bold">{{ $k->sisa_cuti }} Hari</td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick='bukaModalEdit(@json($k))' class="text-yellow-600 bg-yellow-50 hover:bg-yellow-100 px-3 py-1.5 rounded-lg text-xs font-bold transition"><i class="fas fa-pen mr-1"></i>Edit</button>
                            <form id="form-hapus-{{ $k->id }}" action="{{ route('superadmin.karyawan.destroy', $k->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" onclick="konfirmasiHapus('{{ $k->id }}', '{{ $k->name }}')" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition"><i class="fas fa-trash mr-1"></i>Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-400">Belum ada data karyawan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH KARYAWAN -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-red-600 rounded-t-2xl">
            <h3 class="text-lg font-bold text-white"><i class="fas fa-user-plus mr-2"></i> Tambah Karyawan Baru</h3>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-white/80 hover:text-white text-xl"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('superadmin.karyawan.store') }}" method="POST" class="p-6 space-y-4" id="formTambah">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">NIK</label>
                    <input type="text" name="nik" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Email Login</label>
                    <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">No. HP</label>
                    <input type="text" name="no_hp" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Penempatan Cabang</label>
                    <select name="cabang_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($cabangs as $cabang)
                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Alamat</label>
                <textarea name="alamat" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 font-bold py-2.5 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button type="submit" class="flex-1 bg-red-600 text-white font-bold py-2.5 rounded-lg hover:bg-red-700 transition">Simpan Karyawan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT KARYAWAN -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-yellow-500 rounded-t-2xl">
            <h3 class="text-lg font-bold text-white"><i class="fas fa-pen mr-2"></i> Edit Data Karyawan</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="text-white/80 hover:text-white text-xl"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEdit" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">NIK</label>
                    <input type="text" name="nik" id="edit_nik" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Email Login</label>
                    <input type="email" name="email" id="edit_email" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm" placeholder="••••••">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">No. HP</label>
                    <input type="text" name="no_hp" id="edit_no_hp" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Penempatan Cabang</label>
                    <select name="cabang_id" id="edit_cabang_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                        @foreach($cabangs as $cabang)
                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Alamat</label>
                <textarea name="alamat" id="edit_alamat" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 font-bold py-2.5 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button type="submit" class="flex-1 bg-yellow-500 text-white font-bold py-2.5 rounded-lg hover:bg-yellow-600 transition">Update Data</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function bukaModalEdit(data) {
    document.getElementById('formEdit').action = '/superadmin/karyawan/' + data.id;
    document.getElementById('edit_nik').value = data.nik || '';
    document.getElementById('edit_name').value = data.name || '';
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_no_hp').value = data.no_hp || '';
    document.getElementById('edit_cabang_id').value = data.cabang_id || '';
    document.getElementById('edit_alamat').value = data.alamat || '';
    document.getElementById('modalEdit').classList.remove('hidden');
}

function konfirmasiHapus(id, nama) {
    Swal.fire({
        title: 'Hapus Karyawan?',
        html: 'Data karyawan <b>' + nama + '</b> akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-hapus-' + id).submit();
        }
    });
}

// SweetAlert confirm untuk form Tambah
document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    let form = this;
    Swal.fire({
        title: 'Tambah Karyawan?',
        text: 'Pastikan data sudah benar sebelum menyimpan.',
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