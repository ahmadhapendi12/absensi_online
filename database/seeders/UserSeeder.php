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
        $cabang = CabangKantor::first();
        $cabangId = $cabang ? $cabang->id : null;

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
                'nik'      => '12345678',
                'name'     => 'Budi Santoso',
                'telphone' => '081298765432',
                'alamat'   => 'Jl. Sudirman No. 45, Jakarta',
            ]
        );

        // 3. Karyawan 2 (Siti)
        $karyawan2 = User::firstOrCreate(
            ['email' => 'siti@gmail.com'],
            [
                'name'      => 'Siti Rahma',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'cabang_id' => $cabangId,
            ]
        );
        $karyawan2->syncRoles(['karyawan']);

        UserProfile::updateOrCreate(
            ['user_id' => $karyawan2->id],
            [
                'nik'      => '87654321',
                'name'     => 'Siti Rahma',
                'telphone' => '081345678912',
                'alamat'   => 'Jl. Gatot Subroto No. 12, Jakarta',
            ]
        );

        $this->command->info('User Seeder (3 Users + Profiles) Berhasil dibuat.');
    }
}