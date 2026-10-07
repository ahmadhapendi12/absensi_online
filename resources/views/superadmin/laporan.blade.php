@extends('layouts.superadmin')
@section('title', 'Laporan Kehadiran')
@section('content')

<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <form action="{{ route('superadmin.laporan.index') }}" method="GET" class="flex flex-wrap gap-4 items-end w-full md:w-auto">
        <div>
            <label class="text-xs font-bold text-gray-500">Cabang</label>
            <select name="cabang_id" class="block border border-gray-300 rounded-lg p-2 text-sm w-48 focus:ring-red-500 focus:border-red-500">
                <option value="">Semua Cabang</option>
                @foreach($cabangs as $cabang)
                    <option value="{{ $cabang->id }}" {{ $cabangId == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500">Dari Tanggal</label>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" required class="block border border-gray-300 rounded-lg p-2 text-sm w-40 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500">Sampai Tanggal</label>
            <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" required class="block border border-gray-300 rounded-lg p-2 text-sm w-40 focus:ring-red-500 focus:border-red-500">
        </div>
        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 flex items-center gap-2 h-10">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
    
    <form action="{{ route('superadmin.laporan.export-pdf') }}" method="POST" class="w-full md:w-auto">
        @csrf
        <input type="hidden" name="cabang_id" value="{{ $cabangId }}">
        <input type="hidden" name="tanggal_mulai" value="{{ $tanggalMulai }}">
        <input type="hidden" name="tanggal_selesai" value="{{ $tanggalSelesai }}">
        <button type="submit" class="w-full md:w-auto bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700 flex items-center gap-2 justify-center h-10">
            <i class="fas fa-file-pdf"></i> Cetak PDF
        </button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-500">
            <tr>
                <th class="p-4 font-bold">Tanggal</th>
                <th class="p-4 font-bold">Nama Karyawan</th>
                <th class="p-4 font-bold">Cabang</th>
                <th class="p-4 font-bold">Jam Masuk</th>
                <th class="p-4 font-bold">Jam Pulang</th>
                <th class="p-4 font-bold">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($dataAbsensi as $absen)
            <tr>
                <td class="p-4">{{ \Carbon\Carbon::parse($absen->tanggal)->format('d/m/Y') }}</td>
                <td class="p-4 font-bold">{{ $absen->user->name }}</td>
                <td class="p-4 font-semibold text-gray-600">{{ $absen->user->cabang->nama_cabang ?? '-' }}</td>
                <td class="p-4 font-mono">{{ $absen->jam_masuk ?? '-' }}</td>
                <td class="p-4 font-mono">{{ $absen->jam_pulang ?? '-' }}</td>
                <td class="p-4">
                    <span class="px-2 py-1 rounded text-xs font-bold {{ $absen->status_masuk == 'Tepat Waktu' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                        {{ $absen->status_masuk }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection