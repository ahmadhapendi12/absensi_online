<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absensi;
use App\Models\User;
use App\Models\JadwalKaryawan;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $karyawans = User::whereHas('roles', function($q) {
            $q->where('name', 'karyawan');
        })->get();

        if ($karyawans->count() > 0) {
            $kemarin = Carbon::yesterday()->toDateString();
            $duaHariLalu = Carbon::now()->subDays(2)->toDateString();
            $tigaHariLalu = Carbon::now()->subDays(3)->toDateString();

            $jadwalKemarin = JadwalKaryawan::where('user_id', $karyawans[0]->id)->first();

            // 1. Absen Hadir Tepat Waktu (Kemarin)
            Absensi::firstOrCreate(
                [
                    'user_id' => $karyawans[0]->id,
                    'tanggal' => $kemarin,
                ],
                [
                    'jadwal_id' => $jadwalKemarin ? $jadwalKemarin->id : null,
                    'jam_masuk' => '05:55:00',
                    'jam_pulang' => '14:05:00',
                    'koordinat_masuk' => '-6.2088,106.8456',
                    'koordinat_pulang' => '-6.2088,106.8456',
                    'status_masuk' => 'Hadir',
                    'menit_terlambat' => 0,
                ]
            );

            // 2. Absen Terlambat (2 Hari Lalu)
            Absensi::firstOrCreate(
                [
                    'user_id' => $karyawans[0]->id,
                    'tanggal' => $duaHariLalu,
                ],
                [
                    'jadwal_id' => $jadwalKemarin ? $jadwalKemarin->id : null,
                    'jam_masuk' => '06:25:00',
                    'jam_pulang' => '14:00:00',
                    'koordinat_masuk' => '-6.2088,106.8456',
                    'koordinat_pulang' => '-6.2088,106.8456',
                    'status_masuk' => 'Terlambat',
                    'menit_terlambat' => 25,
                ]
            );

            // 3. Absen Izin (3 Hari Lalu)
            if (isset($karyawans[1])) {
                Absensi::firstOrCreate(
                    [
                        'user_id' => $karyawans[1]->id,
                        'tanggal' => $tigaHariLalu,
                    ],
                    [
                        'status_masuk' => 'Izin',
                        'menit_terlambat' => 0,
                    ]
                );
            } else {
                Absensi::firstOrCreate(
                    [
                        'user_id' => $karyawans[0]->id,
                        'tanggal' => $tigaHariLalu,
                    ],
                    [
                        'status_masuk' => 'Sakit',
                        'menit_terlambat' => 0,
                    ]
                );
            }
        }
    }
}
