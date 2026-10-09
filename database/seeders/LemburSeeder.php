<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lembur;
use App\Models\User;
use Carbon\Carbon;

class LemburSeeder extends Seeder
{
    public function run(): void
    {
        $karyawans = User::whereHas('roles', function($q) {
            $q->where('name', 'karyawan');
        })->get();

        $superadmin = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['superadmin', 'super_admin']);
        })->first();

        if ($karyawans->count() > 0) {
            $kemarin = Carbon::yesterday()->toDateString();
            $duaHariLalu = Carbon::now()->subDays(2)->toDateString();
            $besok = Carbon::now()->addDay()->toDateString();

            // 1. Lembur Selesai & Disetujui (Kemarin)
            Lembur::firstOrCreate(
                [
                    'user_id' => $karyawans[0]->id,
                    'tanggal' => $kemarin,
                ],
                [
                    'alasan_lembur' => 'Mengejar target stock opname bulanan',
                    'status_pengajuan' => 'Disetujui',
                    'disetujui_oleh' => $superadmin ? $superadmin->id : null,
                    'jam_mulai_aktual' => '16:00:00',
                    'jam_selesai_aktual' => '19:00:00',
                    'koordinat_mulai' => '-6.2088,106.8456',
                    'koordinat_selesai' => '-6.2088,106.8456',
                    'durasi_menit' => 180,
                ]
            );

            // 2. Lembur Pending (Besok)
            Lembur::firstOrCreate(
                [
                    'user_id' => $karyawans[0]->id,
                    'tanggal' => $besok,
                ],
                [
                    'alasan_lembur' => 'Persiapan event promosi cabang',
                    'status_pengajuan' => 'Pending',
                    'jam_mulai_aktual' => '17:00:00',
                    'jam_selesai_aktual' => '20:00:00',
                    'durasi_menit' => 180,
                ]
            );

            // 3. Lembur Karyawan 2 (Pending / Ditolak)
            if (isset($karyawans[1])) {
                Lembur::firstOrCreate(
                    [
                        'user_id' => $karyawans[1]->id,
                        'tanggal' => $duaHariLalu,
                    ],
                    [
                        'alasan_lembur' => 'Revisi laporan penjualan harian',
                        'status_pengajuan' => 'Ditolak',
                        'disetujui_oleh' => $superadmin ? $superadmin->id : null,
                        'durasi_menit' => 60,
                    ]
                );
            }
        }
    }
}
