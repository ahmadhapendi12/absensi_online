<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CabangKantor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cabang = CabangKantor::first();
        $cabangId = $cabang ? $cabang->id : null;


        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'cabang_id' => $cabangId,
            ]
        );
        $superadmin->syncRoles(['super_admin', 'superadmin']);


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

        $this->command->info('User Seeder (3 Users) Berhasil dibuat.');
    }
}