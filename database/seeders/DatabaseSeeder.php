<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CabangKantor;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Cabang Pusat Terlebih Dahulu
        $cabangPusat = CabangKantor::create([
            'nama_cabang' => 'Kantor Pusat Lazatto',
            'latitude' => '-6.175110', // Contoh titik koordinat Monas
            'longitude' => '106.827153',
            'radius_meter' => 100,
        ]);

        // 2. Buat Akun Superadmin
        User::create([
            'cabang_id' => $cabangPusat->id,
            'nik' => 'SA-001',
            'name' => 'Bapak Deden (Superadmin)',
            'email' => 'deden@lazatto.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'superadmin',
            'sisa_cuti' => 12,
        ]);

        // 3. Buat Akun Karyawan Dummy (Untuk Tes Tampilan Nanti)
        User::create([
            'cabang_id' => $cabangPusat->id,
            'nik' => 'EMP-001',
            'name' => 'Ahmad Hapendi (Karyawan)',
            'email' => 'ahmad@lazatto.com',
            'password' => Hash::make('karyawan123'),
            'role' => 'karyawan',
            'sisa_cuti' => 12,
        ]);
    }
    
}