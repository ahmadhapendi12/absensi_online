<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalKaryawan;
use App\Models\User;
use App\Models\MasterShift;
use App\Models\RiwayatJadwalKaryawan;
use Carbon\Carbon;

class JadwalKaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $budi = User::where('email', 'karyawan@gmail.com')->first();
        $siti = User::where('email', 'siti@gmail.com')->first();

        $shiftPagi = MasterShift::where('nama_shift', 'Shift Pagi')->first();
        $shiftSore = MasterShift::where('nama_shift', 'Shift Sore')->first();

        RiwayatJadwalKaryawan::query()->delete();

        JadwalKaryawan::withoutEvents(function () use ($budi, $siti, $shiftPagi, $shiftSore) {
            if ($budi && $shiftPagi) {
                JadwalKaryawan::updateOrCreate(
                    ['user_id' => $budi->id],
                    [
                        'shift_id' => $shiftPagi->id,
                        'hari_libur_rutin' => 'senin',
                    ]
                );
            }

            if ($siti && $shiftSore) {
                JadwalKaryawan::updateOrCreate(
                    ['user_id' => $siti->id],
                    [
                        'shift_id' => $shiftSore->id,
                        'hari_libur_rutin' => 'minggu',
                    ]
                );
            }
        });

        if ($budi && $shiftPagi) {
            RiwayatJadwalKaryawan::create([
                'user_id' => $budi->id,
                'shift_id' => $shiftPagi->id,
                'hari_libur_rutin' => 'senin',
                'berlaku_dari' => '2026-10-01',
                'berlaku_sampai' => null,
            ]);
        }

        if ($siti && $shiftSore) {
            RiwayatJadwalKaryawan::create([
                'user_id' => $siti->id,
                'shift_id' => $shiftSore->id,
                'hari_libur_rutin' => 'minggu',
                'berlaku_dari' => '2026-10-01',
                'berlaku_sampai' => null,
            ]);
        }
    }
}
