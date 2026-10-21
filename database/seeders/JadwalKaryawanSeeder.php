<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalKaryawan;
use App\Models\User;
use App\Models\MasterShift;
use App\Models\RiwayatJadwalKaryawan;

class JadwalKaryawanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data karyawan dari database berdasarkan email
        $budi = User::where('email', 'karyawan@gmail.com')->first();
        $siti = User::where('email', 'siti@gmail.com')->first();
        $tono = User::where('email', 'tono@gmail.com')->first(); 

        // 2. Ambil data master shift (Shift 1, Shift 2, Shift 3)
        $shift1 = MasterShift::where('nama_shift', 'Shift 1')->first();
        $shift2 = MasterShift::where('nama_shift', 'Shift 2')->first();
        $shift3 = MasterShift::where('nama_shift', 'Shift 3')->first();

        // 3. Assign Jadwal Utama Karyawan tanpa men-trigger event
        JadwalKaryawan::withoutEvents(function () use ($budi, $siti, $tono, $shift1, $shift2, $shift3) {
            if ($budi && $shift1) {
                JadwalKaryawan::updateOrCreate(
                    ['user_id' => $budi->id],
                    [
                        'shift_id' => $shift1->id,
                        'hari_libur_rutin' => 'senin',
                    ]
                );
            }

            if ($siti && $shift2) {
                JadwalKaryawan::updateOrCreate(
                    ['user_id' => $siti->id],
                    [
                        'shift_id' => $shift2->id,
                        'hari_libur_rutin' => 'minggu',
                    ]
                );
            }

            if ($tono && $shift3) {
                JadwalKaryawan::updateOrCreate(
                    ['user_id' => $tono->id],
                    [
                        'shift_id' => $shift3->id,
                        'hari_libur_rutin' => 'selasa',
                    ]
                );
            }
        });

        // 4. Assign Riwayat Jadwal Karyawan (Gunakan updateOrCreate agar tidak duplikat)
        if ($budi && $shift1) {
            RiwayatJadwalKaryawan::updateOrCreate(
                [
                    'user_id' => $budi->id,
                    'berlaku_dari' => '2026-10-01'
                ],
                [
                    'shift_id' => $shift1->id,
                    'hari_libur_rutin' => 'senin',
                    'berlaku_sampai' => null,
                ]
            );
        }

        if ($siti && $shift2) {
            RiwayatJadwalKaryawan::updateOrCreate(
                [
                    'user_id' => $siti->id,
                    'berlaku_dari' => '2026-10-01'
                ],
                [
                    'shift_id' => $shift2->id,
                    'hari_libur_rutin' => 'minggu',
                    'berlaku_sampai' => null,
                ]
            );
        }

        if ($tono && $shift3) {
            RiwayatJadwalKaryawan::updateOrCreate(
                [
                    'user_id' => $tono->id,
                    'berlaku_dari' => '2026-10-01'
                ],
                [
                    'shift_id' => $shift3->id,
                    'hari_libur_rutin' => 'selasa',
                    'berlaku_sampai' => null,
                ]
            );
        }
        
        $this->command->info('Jadwal Karyawan Berhasil di-seed.');
    }
}