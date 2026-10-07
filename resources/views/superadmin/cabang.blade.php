@extends('layouts.superadmin')
@section('title', 'Kelola Cabang & GPS')
@section('content')

<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <h3 class="font-bold text-[#2B3674] mb-4">Tambah Titik Cabang Baru</h3>
    <form action="{{ route('superadmin.cabang.store') }}" method="POST" class="grid grid-cols-5 gap-4 items-end">
        @csrf
        <div class="col-span-2"><label class="text-xs font-bold text-gray-500">Nama Cabang</label><input type="text" name="nama_cabang" placeholder="Misal: Bandung" required class="w-full border-gray-200 rounded-lg p-2 text-sm"></div>
        <div><label class="text-xs font-bold text-gray-500">Latitude</label><input type="text" name="latitude" required class="w-full border-gray-200 rounded-lg p-2 text-sm"></div>
        <div><label class="text-xs font-bold text-gray-500">Longitude</label><input type="text" name="longitude" required class="w-full border-gray-200 rounded-lg p-2 text-sm"></div>
        <div><label class="text-xs font-bold text-gray-500">Radius (Meter)</label><input type="number" name="radius_meter" value="100" required class="w-full border-gray-200 rounded-lg p-2 text-sm"></div>
        <button type="submit" class="col-span-5 bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700">Simpan Cabang</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-500">
            <tr>
                <th class="p-4 font-bold">Nama Cabang</th>
                <th class="p-4 font-bold">Titik Koordinat (Lat, Lng)</th>
                <th class="p-4 font-bold">Batas Radius</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($cabangs as $c)
            <tr>
                <td class="p-4 font-bold text-gray-800">{{ $c->nama_cabang }}</td>
                <td class="p-4 font-mono text-xs text-gray-500">{{ $c->latitude }}, {{ $c->longitude }}</td>
                <td class="p-4"><span class="bg-green-50 text-green-600 px-2 py-1 rounded text-xs font-bold">{{ $c->radius_meter }} Meter</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection