<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\CabangKantor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cabangPusat = CabangKantor::where('nama_cabang', 'Kantor Pusat Jakarta')->first();
        $cabangBandung = CabangKantor::where('nama_cabang', 'Cabang Bandung')->first();
        $cabangId = $cabangPusat ? $cabangPusat->id : null;
        $cabangBandungId = $cabangBandung ? $cabangBandung->id : $cabangId;

        // 1. Superadmin
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'cabang_id' => $cabangId,
            ]
        );
        $superadmin->syncRoles(['superadmin']);

        UserProfile::updateOrCreate(
            ['user_id' => $superadmin->id],
            [
                'nik'      => '99999999',
                'name'     => 'Super Admin',
                'telphone' => '081234567890',
                'alamat'   => 'Kantor Pusat Jakarta',
            ]
        );

        // 2. Karyawan 1 (Budi)
        $karyawan1 = User::firstOrCreate(
            ['email' => 'karyawan@gmail.com'],
            [
                'name'      => 'Budi Santoso',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'cabang_id' => $cabangId,
            ]
        );
        $karyawan1->syncRoles(['karyawan']);

        UserProfile::updateOrCreate(
            ['user_id' => $karyawan1->id],
            [
                'nik'               => '12345678',
                'name'              => 'Budi Santoso',
                'tanggal_bergabung' => '2026-10-01',
                'telphone'          => '081298765432',
                'alamat'            => 'Jl. Sudirman No. 45, Jakarta',
            ]
        );

        // 3. Karyawan 2 (Siti)
        $karyawan2 = User::firstOrCreate(
            ['email' => 'siti@gmail.com'],
            [
                'name'      => 'Siti Rahma',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'cabang_id' => $cabangBandungId,
            ]
        );
        $karyawan2->syncRoles(['karyawan']);

        UserProfile::updateOrCreate(
            ['user_id' => $karyawan2->id],
            [
                'nik'               => '87654321',
                'name'              => 'Siti Rahma',
                'tanggal_bergabung' => '2026-10-01',
                'telphone'          => '081345678912',
                'alamat'            => 'Jl. Riau No. 12, Bandung',
            ]
        );

        $this->command->info('User Seeder (3 Users + Profiles) Berhasil dibuat.');
    }
}