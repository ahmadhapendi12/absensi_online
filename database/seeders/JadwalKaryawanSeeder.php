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

        // 2. Ambil data master shift sesuai cabang masing-masing karyawan
        $shiftBudi = $budi ? (MasterShift::where('cabang_id', $budi->cabang_id)->where('nama_shift', 'Shift 1')->first() ?? MasterShift::where('nama_shift', 'Shift 1')->first()) : null;
        $shiftSiti = $siti ? (MasterShift::where('cabang_id', $siti->cabang_id)->where('nama_shift', 'Shift 2')->first() ?? MasterShift::where('nama_shift', 'Shift 2')->first()) : null;
        $shiftTono = $tono ? (MasterShift::where('cabang_id', $tono->cabang_id)->where('nama_shift', 'Shift 3')->first() ?? MasterShift::where('nama_shift', 'Shift 3')->first()) : null;

        // 3. Assign Jadwal Utama Karyawan tanpa men-trigger event
        JadwalKaryawan::withoutEvents(function () use ($budi, $siti, $tono, $shiftBudi, $shiftSiti, $shiftTono) {
            if ($budi && $shiftBudi) {
                JadwalKaryawan::updateOrCreate(
                    ['user_id' => $budi->id],
                    [
                        'shift_id' => $shiftBudi->id,
                    ]
                );
            }

            if ($siti && $shiftSiti) {
                JadwalKaryawan::updateOrCreate(
                    ['user_id' => $siti->id],
                    [
                        'shift_id' => $shiftSiti->id,
                    ]
                );
            }

            if ($tono && $shiftTono) {
                JadwalKaryawan::updateOrCreate(
                    ['user_id' => $tono->id],
                    [
                        'shift_id' => $shiftTono->id,
                    ]
                );
            }
        });

        // 4. Assign Riwayat Jadwal Karyawan (Gunakan updateOrCreate agar tidak duplikat)
        if ($budi && $shiftBudi) {
            RiwayatJadwalKaryawan::updateOrCreate(
                [
                    'user_id' => $budi->id,
                    'berlaku_dari' => '2026-10-01'
                ],
                [
                    'shift_id' => $shiftBudi->id,
                    'berlaku_sampai' => null,
                ]
            );
        }

        if ($siti && $shiftSiti) {
            RiwayatJadwalKaryawan::updateOrCreate(
                [
                    'user_id' => $siti->id,
                    'berlaku_dari' => '2026-10-01'
                ],
                [
                    'shift_id' => $shiftSiti->id,
                    'berlaku_sampai' => null,
                ]
            );
        }

        if ($tono && $shiftTono) {
            RiwayatJadwalKaryawan::updateOrCreate(
                [
                    'user_id' => $tono->id,
                    'berlaku_dari' => '2026-10-01'
                ],
                [
                    'shift_id' => $shiftTono->id,
                    'berlaku_sampai' => null,
                ]
            );
        }
        
        $this->command->info('Jadwal Karyawan Berhasil di-seed.');
    }
}