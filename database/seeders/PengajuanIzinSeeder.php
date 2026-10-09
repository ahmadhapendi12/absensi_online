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
        $karyawans = User::whereHas('roles', function($q) {
            $q->where('name', 'karyawan');
        })->get();

        $superadmin = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['superadmin', 'super_admin']);
        })->first();

        if ($karyawans->count() > 0) {
            // 1. Pengajuan Sakit (Pending)
            PengajuanIzin::firstOrCreate(
                [
                    'user_id' => $karyawans[0]->id,
                    'tanggal_mulai' => Carbon::now()->addDays(3)->toDateString(),
                ],
                [
                    'jenis' => 'Sakit',
                    'tanggal_selesai' => Carbon::now()->addDays(4)->toDateString(),
                    'alasan' => 'Demam dan flu tinggi',
                    'status' => 'Pending',
                ]
            );

            // 2. Pengajuan Cuti (Disetujui)
            PengajuanIzin::firstOrCreate(
                [
                    'user_id' => $karyawans[0]->id,
                    'tanggal_mulai' => Carbon::now()->addDays(10)->toDateString(),
                ],
                [
                    'jenis' => 'Cuti',
                    'tanggal_selesai' => Carbon::now()->addDays(12)->toDateString(),
                    'alasan' => 'Acara keluarga di kampung',
                    'status' => 'Disetujui',
                    'disetujui_oleh' => $superadmin ? $superadmin->id : null,
                ]
            );

            // 3. Pengajuan Izin Karyawan 2 (Pending)
            if (isset($karyawans[1])) {
                PengajuanIzin::firstOrCreate(
                    [
                        'user_id' => $karyawans[1]->id,
                        'tanggal_mulai' => Carbon::now()->addDays(5)->toDateString(),
                    ],
                    [
                        'jenis' => 'Izin',
                        'tanggal_selesai' => Carbon::now()->addDays(5)->toDateString(),
                        'alasan' => 'Mengurus perpanjangan SIM',
                        'status' => 'Pending',
                    ]
                );
            }
        }
    }
}
