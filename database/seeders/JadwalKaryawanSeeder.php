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
            $hariIni = Carbon::now()->toDateString();
            $besok = Carbon::now()->addDay()->toDateString();
            $lusa = Carbon::now()->addDays(2)->toDateString();

            // 1. Jadwal Hari Ini Karyawan 1 (Shift Pagi)
            JadwalKaryawan::firstOrCreate(
                [
                    'user_id' => $karyawans[0]->id,
                    'tanggal_kerja' => $hariIni,
                ],
                [
                    'shift_id' => $shifts[0]->id,
                ]
            );

            // 2. Jadwal Besok Karyawan 1 (Shift Sore)
            JadwalKaryawan::firstOrCreate(
                [
                    'user_id' => $karyawans[0]->id,
                    'tanggal_kerja' => $besok,
                ],
                [
                    'shift_id' => isset($shifts[1]) ? $shifts[1]->id : $shifts[0]->id,
                ]
            );

            // 3. Jadwal Hari Ini Karyawan 2 (Shift Sore/Pagi)
            if (isset($karyawans[1])) {
                JadwalKaryawan::firstOrCreate(
                    [
                        'user_id' => $karyawans[1]->id,
                        'tanggal_kerja' => $hariIni,
                    ],
                    [
                        'shift_id' => isset($shifts[1]) ? $shifts[1]->id : $shifts[0]->id,
                    ]
                );
            }
        }
    }
}
