<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterShift;
use App\Models\CabangKantor;

class MasterShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan shift lama terlebih dahulu
        // (Hanya shift khusus per cabang yang aktif, tidak ada shift umum/global tanpa cabang)
        $cabangs = CabangKantor::all();

        foreach ($cabangs as $cabang) {
            $is3Shift = (
                stripos($cabang->nama_cabang, 'Ciruas') !== false ||
                stripos($cabang->nama_cabang, 'Cikande Ambon') !== false ||
                $cabang->is_24_jam
            );

            if ($is3Shift) {
                // Cabang 3 Shift: 07:00 - 15:00, 15:00 - 23:00, 23:00 - 07:00 (lintas hari)
                // Semua shift diatur waktu buka absen standar 60 menit (1 jam)
                $shifts = [
                    [
                        'nama_shift' => 'Shift 1',
                        'jam_masuk' => '07:00:00',
                        'jam_pulang' => '15:00:00',
                        'buka_absen_menit' => 60,
                        'toleransi_terlambat_menit' => 15,
                        'lintas_hari' => false,
                    ],
                    [
                        'nama_shift' => 'Shift 2',
                        'jam_masuk' => '15:00:00',
                        'jam_pulang' => '23:00:00',
                        'buka_absen_menit' => 60,
                        'toleransi_terlambat_menit' => 15,
                        'lintas_hari' => false,
                    ],
                    [
                        'nama_shift' => 'Shift 3',
                        'jam_masuk' => '23:00:00',
                        'jam_pulang' => '07:00:00',
                        'buka_absen_menit' => 60,
                        'toleransi_terlambat_menit' => 15,
                        'lintas_hari' => true,
                    ],
                ];
            } else {
                // Cabang 2 Shift: 06:00 - 14:00, 14:00 - 23:00
                // Semua shift diatur waktu buka absen standar 60 menit (1 jam)
                $shifts = [
                    [
                        'nama_shift' => 'Shift 1',
                        'jam_masuk' => '06:00:00',
                        'jam_pulang' => '14:00:00',
                        'buka_absen_menit' => 60,
                        'toleransi_terlambat_menit' => 15,
                        'lintas_hari' => false,
                    ],
                    [
                        'nama_shift' => 'Shift 2',
                        'jam_masuk' => '14:00:00',
                        'jam_pulang' => '23:00:00',
                        'buka_absen_menit' => 60,
                        'toleransi_terlambat_menit' => 15,
                        'lintas_hari' => false,
                    ],
                ];
            }

            foreach ($shifts as $data) {
                MasterShift::updateOrCreate(
                    [
                        'cabang_id' => $cabang->id,
                        'nama_shift' => $data['nama_shift'],
                    ],
                    $data
                );
            }
        }
    }
}