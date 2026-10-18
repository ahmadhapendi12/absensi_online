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
        Absensi::query()->delete();

        $budi = User::where('email', 'karyawan@gmail.com')->first();

        if ($budi) {
            $jadwalBudi = JadwalKaryawan::where('user_id', $budi->id)->first();

            // 1. Absen Hadir Tepat Waktu (Kamis, 15 Oktober 2026)
            Absensi::updateOrCreate(
                [
                    'user_id' => $budi->id,
                    'tanggal' => '2026-10-15',
                ],
                [
                    'jadwal_id' => $jadwalBudi?->id,
                    'jam_masuk' => '05:55:00',
                    'jam_pulang' => '14:05:00',
                    'koordinat_masuk' => '-6.2088,106.8456',
                    'koordinat_pulang' => '-6.2088,106.8456',
                    'status_masuk' => 'Hadir',
                    'menit_terlambat' => 0,
                ]
            );

            // 2. Absen Terlambat (Jumat, 16 Oktober 2026)
            Absensi::updateOrCreate(
                [
                    'user_id' => $budi->id,
                    'tanggal' => '2026-10-16',
                ],
                [
                    'jadwal_id' => $jadwalBudi?->id,
                    'jam_masuk' => '06:12:00',
                    'jam_pulang' => '14:00:00',
                    'koordinat_masuk' => '-6.2088,106.8456',
                    'koordinat_pulang' => '-6.2088,106.8456',
                    'status_masuk' => 'Terlambat',
                    'menit_terlambat' => 12,
                ]
            );
        }
    }
}
