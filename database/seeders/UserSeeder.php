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
        // Sesuaikan nama cabang dengan yang ada di CabangKantorSeeder terbaru
        $cabangCijeruk = CabangKantor::where('nama_cabang', 'Lazatto Cijeruk Chicken And Burger')->first();
        $cabangCiruas = CabangKantor::where('nama_cabang', 'Lazatto Ciruas Chicken and Burger')->first();
        $cabangCikande = CabangKantor::where('nama_cabang', 'Lazatto Cikande Ambon Chicken and Burger')->first();
        
        $cabangCijerukId = $cabangCijeruk ? $cabangCijeruk->id : null;
        $cabangCiruasId = $cabangCiruas ? $cabangCiruas->id : $cabangCijerukId;
        $cabangCikandeId = $cabangCikande ? $cabangCikande->id : $cabangCijerukId;

        // 1. Superadmin
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'cabang_id' => null, // Default ke Cijeruk
            ]
        );
        $superadmin->syncRoles(['superadmin']);

        UserProfile::updateOrCreate(
            ['user_id' => $superadmin->id],
            [
                'nik'      => '99999999',
                'name'     => 'Super Admin',
                'telphone' => '081234567890',
                'alamat'   => 'Kantor Pusat Lazatto',
            ]
        );

        // 2. Karyawan 1 (Budi) - Ditempatkan di Cijeruk
        $karyawan1 = User::firstOrCreate(
            ['email' => 'karyawan@gmail.com'],
            [
                'name'      => 'Budi Santoso',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'cabang_id' => $cabangCijerukId,
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
                'alamat'            => 'Jl. Sudirman No. 45',
            ]
        );

        // 3. Karyawan 2 (Siti) - Ditempatkan di Ciruas
        $karyawan2 = User::firstOrCreate(
            ['email' => 'siti@gmail.com'],
            [
                'name'      => 'Siti Rahma',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'cabang_id' => $cabangCiruasId,
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
                'alamat'            => 'Kawasan Ciruas',
            ]
        );

        // 4. Karyawan 3 (Tono) - Ditempatkan di Cikande Ambon (Shift 3 Malam)
        $karyawan3 = User::firstOrCreate(
            ['email' => 'tono@gmail.com'],
            [
                'name'      => 'Tono Supriyanto',
                'password'  => Hash::make('password123'),
                'status'    => 'active',
                'cabang_id' => $cabangCikandeId,
            ]
        );
        $karyawan3->syncRoles(['karyawan']);

        UserProfile::updateOrCreate(
            ['user_id' => $karyawan3->id],
            [
                'nik'               => '11223344',
                'name'              => 'Tono Supriyanto',
                'tanggal_bergabung' => '2026-10-01',
                'telphone'          => '081555666777',
                'alamat'            => 'Kawasan Cikande Ambon',
            ]
        );

        $this->command->info('User Seeder (4 Users + Profiles) Berhasil dibuat.');
    }
}