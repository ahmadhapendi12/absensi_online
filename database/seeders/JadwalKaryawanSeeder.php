<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalKaryawan;
use App\Models\User;
use App\Models\MasterShift;
use Carbon\Carbon;

class JadwalKaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $karyawans = User::whereHas('roles', function($q) {
            $q->where('name', 'karyawan');
        })->get();

        $shifts = MasterShift::all();

        if ($karyawans->count() > 0 && $shifts->count() > 0) {
            // 1. Jadwal Karyawan 1 (Shift Pagi)
            JadwalKaryawan::firstOrCreate(
                ['user_id' => $karyawans[0]->id],
                ['shift_id' => $shifts[0]->id]
            );

            // 2. Jadwal Karyawan 2 (Shift Sore)
            if (isset($karyawans[1])) {
                JadwalKaryawan::firstOrCreate(
                    ['user_id' => $karyawans[1]->id],
                    ['shift_id' => isset($shifts[1]) ? $shifts[1]->id : $shifts[0]->id]
                );
            }
        }
    }
}
