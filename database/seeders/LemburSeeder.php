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
        Lembur::query()->delete();

        $budi = User::where('email', 'karyawan@gmail.com')->first();
        $siti = User::where('email', 'siti@gmail.com')->first();

        $superadmin = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['superadmin', 'super_admin']);
        })->first();

        // 1. Lembur Disetujui (Budi Santoso - Kamis, 15 Oktober 2026, Pasca-Shift Pagi: 14:30 - 16:30)
        if ($budi) {
            Lembur::updateOrCreate(
                [
                    'user_id' => $budi->id,
                    'tanggal' => '2026-10-15',
                ],
                [
                    'alasan_lembur' => 'Membantu persiapan restock bahan ayam potong dan bumbu marinasi outlet',
                    'status_pengajuan' => 'Disetujui',
                    'disetujui_oleh' => $superadmin ? $superadmin->id : null,
                    'jam_mulai_aktual' => '14:30:00',
                    'jam_selesai_aktual' => '16:30:00',
                    'koordinat_mulai' => '-6.2088,106.8456',
                    'koordinat_selesai' => '-6.2088,106.8456',
                    'durasi_menit' => 120,
                ]
            );
        }

        // 2. Lembur Pending (Siti Rahma - Selasa, 20 Oktober 2026, Pra-Shift Sore: 11:30 - 13:30)
        if ($siti) {
            Lembur::updateOrCreate(
                [
                    'user_id' => $siti->id,
                    'tanggal' => '2026-10-20',
                ],
                [
                    'alasan_lembur' => 'Pembersihan berkala etalase display dan deep fryer outlet',
                    'status_pengajuan' => 'Pending',
                    'jam_mulai_aktual' => '11:30:00',
                    'jam_selesai_aktual' => '13:30:00',
                    'koordinat_mulai' => '-6.2088,106.8456',
                    'koordinat_selesai' => '-6.2088,106.8456',
                    'durasi_menit' => 120,
                ]
            );
        }
    }
}
