<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CabangKantorSeeder::class,
            UserSeeder::class,
            MasterShiftSeeder::class,
            JadwalKaryawanSeeder::class,
            PengajuanIzinSeeder::class,
            AbsensiSeeder::class,
            LemburSeeder::class,
        ]);
    }
}