@extends('layouts.superadmin')
@section('title', 'Laporan Kehadiran')
@section('content')

<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 flex justify-between items-end">
    <form action="{{ route('superadmin.laporan.export-pdf') }}" method="POST" class="flex gap-4 items-end">
        @csrf
        <div><label class="text-xs font-bold text-gray-500">Dari Tanggal</label><input type="date" name="tanggal_mulai" required class="border-gray-200 rounded-lg p-2 text-sm w-40"></div>
        <div><label class="text-xs font-bold text-gray-500">Sampai Tanggal</label><input type="date" name="tanggal_selesai" required class="border-gray-200 rounded-lg p-2 text-sm w-40"></div>
        <button type="submit" class="bg-red-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-600 flex items-center gap-2">
            <i class="fas fa-file-pdf"></i> Ekstrak ke PDF
        </button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-500">
            <tr>
                <th class="p-4 font-bold">Tanggal</th>
                <th class="p-4 font-bold">Nama Karyawan</th>
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