@extends('layouts.superadmin')

@section('title', 'Manajemen Jadwal Karyawan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-red-900">Jadwal Shift Karyawan</h2>
        <p class="text-sm text-gray-500">Atur jadwal kerja harian karyawan</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <span class="block sm:inline">{{ session('error') }}</span>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Form Tambah Jadwal -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 md:col-span-1 h-fit sticky top-24">
        <h3 class="font-bold text-red-900 mb-4 border-b pb-2">Tambah Jadwal Baru</h3>
        <form action="{{ route('superadmin.jadwal.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Karyawan</label>
                <select name="user_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id }}">{{ $k->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Shift</label>
                <select name="shift_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Pilih Shift --</option>
                    @foreach($shifts as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_shift }} ({{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_pulang)->format('H:i') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kerja</label>
                <input type="date" name="tanggal_kerja" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>
            <button type="submit" class="w-full bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700 transition">
                Simpan Jadwal
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Jadwal -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 md:col-span-2">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h3 class="font-bold text-red-900">Daftar Jadwal</h3>
            
            <form action="{{ route('superadmin.jadwal.index') }}" method="GET" class="flex gap-2">
                <select name="user_id" class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Semua Karyawan</option>
                    @foreach($karyawans as $k)
                        <option value="{{ $k->id }}" {{ request('user_id') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm font-bold hover:bg-yellow-600"><i class="fas fa-filter"></i> Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-red-50 text-red-900">
                    <tr>
                        <th class="p-3 rounded-tl-lg">Nama Karyawan</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Shift</th>
                        <th class="p-3 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $jadwal)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="p-3 font-semibold">{{ $jadwal->user->name ?? '-' }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($jadwal->tanggal_kerja)->translatedFormat('d M Y') }}</td>
                        <td class="p-3">
                            <span class="bg-orange-100 text-orange-800 py-1 px-2 rounded-full text-xs font-bold">
                                {{ $jadwal->shift->nama_shift ?? '-' }}
                            </span>
                            <br>
                            <span class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($jadwal->shift->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->shift->jam_pulang)->format('H:i') }}
                            </span>
                        </td>
                        <td class="p-3">
                            <form id="form-hapus-jadwal-{{ $jadwal->id }}" action="{{ route('superadmin.jadwal.destroy', $jadwal->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="hapusJadwal('{{ $jadwal->id }}', '{{ $jadwal->user->name ?? '' }}')" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2 py-1 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-400">Belum ada data jadwal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $jadwals->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function hapusJadwal(id, nama) {
    Swal.fire({
        title: 'Hapus Jadwal?',
        html: 'Jadwal untuk <b>' + nama + '</b> akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-hapus-jadwal-' + id).submit();
        }
    });
}
</script>
@endpush

