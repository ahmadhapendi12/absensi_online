@extends('layouts.superadmin')
@section('title', 'Kelola Karyawan')
@section('content')

<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <h3 class="font-bold text-[#2B3674] mb-4">Tambah Karyawan Baru</h3>
    <form action="{{ route('superadmin.karyawan.store') }}" method="POST" class="grid grid-cols-4 gap-4 items-end">
        @csrf
        <div><label class="text-xs font-bold text-gray-500">NIK</label><input type="text" name="nik" required class="w-full border-gray-200 rounded-lg p-2 text-sm"></div>
        <div><label class="text-xs font-bold text-gray-500">Nama Lengkap</label><input type="text" name="name" required class="w-full border-gray-200 rounded-lg p-2 text-sm"></div>
        <div><label class="text-xs font-bold text-gray-500">Email Login</label><input type="email" name="email" required class="w-full border-gray-200 rounded-lg p-2 text-sm"></div>
        <div><label class="text-xs font-bold text-gray-500">Password</label><input type="password" name="password" required class="w-full border-gray-200 rounded-lg p-2 text-sm"></div>
        <div class="col-span-3">
            <label class="text-xs font-bold text-gray-500">Penempatan Cabang</label>
            <select name="cabang_id" required class="w-full border-gray-200 rounded-lg p-2 text-sm">
                @foreach($cabangs as $cabang)
                <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700">Simpan Akun</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-500">
            <tr>
                <th class="p-4 font-bold">NIK</th>
                <th class="p-4 font-bold">Nama Karyawan</th>
                <th class="p-4 font-bold">Cabang</th>
                <th class="p-4 font-bold">Sisa Cuti</th>
                <th class="p-4 font-bold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($karyawans as $k)
            <tr>
                <td class="p-4">{{ $k->nik }}</td>
                <td class="p-4 font-bold text-gray-800">{{ $k->name }}<br><span class="text-xs text-gray-400">{{ $k->email }}</span></td>
                <td class="p-4">{{ $k->cabang->nama_cabang ?? 'Pusat' }}</td>
                <td class="p-4">{{ $k->sisa_cuti }} Hari</td>
                <td class="p-4"><button class="text-blue-500 text-xs font-bold bg-blue-50 px-3 py-1 rounded-md">Edit</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection