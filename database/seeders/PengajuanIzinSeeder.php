<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanIzin;
use App\Models\User;
use Carbon\Carbon;

class PengajuanIzinSeeder extends Seeder
{
    public function run(): void
    {
        PengajuanIzin::query()->delete();

        $budi = User::where('email', 'karyawan@gmail.com')->first();
        $siti = User::where('email', 'siti@gmail.com')->first();

        $superadmin = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['superadmin', 'super_admin']);
        })->first();

        // 1. Pengajuan Cuti Disetujui (Budi Santoso - 22 s/d 23 Oktober 2026)
        if ($budi) {
            PengajuanIzin::updateOrCreate(
                [
                    'user_id' => $budi->id,
                    'tanggal_mulai' => '2026-10-22',
                ],
                [
                    'jenis' => 'Cuti',
                    'tanggal_selesai' => '2026-10-23',
                    'alasan' => 'Acara syukuran keluarga di luar kota',
                    'status' => 'Disetujui',
                    'disetujui_oleh' => $superadmin ? $superadmin->id : null,
                ]
            );
        }

        // 2. Pengajuan Izin Pending (Siti Rahma - 26 Oktober 2026)
        if ($siti) {
            PengajuanIzin::updateOrCreate(
                [
                    'user_id' => $siti->id,
                    'tanggal_mulai' => '2026-10-26',
                ],
                [
                    'jenis' => 'Izin',
                    'tanggal_selesai' => '2026-10-26',
                    'alasan' => 'Perpanjangan SIM di layanan Samsat Keliling',
                    'status' => 'Pending',
                ]
            );
        }
    }
}
