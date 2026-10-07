@extends('layouts.superadmin')
@section('title', 'Validasi Pengajuan')
@section('content')

<!-- Antrean Izin/Cuti/Sakit -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-[#2B3674]"><i class="fas fa-file-medical mr-2"></i> Antrean Izin, Sakit & Cuti</div>
    <table class="w-full text-left text-sm">
        <thead class="text-gray-500">
            <tr>
                <th class="p-4 font-bold">Karyawan</th>
                <th class="p-4 font-bold">Jenis</th>
                <th class="p-4 font-bold">Tanggal</th>
                <th class="p-4 font-bold">Alasan</th>
                <th class="p-4 font-bold text-right">Aksi (ACC)</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($izins as $izin)
            <tr>
                <td class="p-4 font-bold text-gray-800">{{ $izin->user->name }}</td>
                <td class="p-4"><span class="bg-yellow-50 text-yellow-600 px-2 py-1 rounded text-xs font-bold">{{ $izin->jenis }}</span></td>
                <td class="p-4">{{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M') }}</td>
                <td class="p-4 text-xs text-gray-500 max-w-xs">{{ $izin->alasan }}</td>
                <td class="p-4 text-right">
                    <form action="{{ route('superadmin.pengajuan.izin.approve', $izin->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs font-bold hover:bg-green-600">Setujui</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="p-4 text-center text-gray-400">Tidak ada pengajuan izin pending.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection