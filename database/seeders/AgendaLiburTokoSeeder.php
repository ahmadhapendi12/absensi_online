<?php

namespace Database\Seeders;

use App\Models\AgendaLiburToko;
use App\Models\CabangKantor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AgendaLiburTokoSeeder extends Seeder
{
    public function run(): void
    {
        AgendaLiburToko::query()->delete();

        $cabang = CabangKantor::first();

        // 1. Agenda Jumsih (Jumat Bersih - Jumat, 23 Oktober 2026)
        AgendaLiburToko::updateOrCreate(
            ['nama_agenda' => 'Jumsih Rutin Pembersihan Outlet'],
            [
                'jenis' => 'jumsih',
                'tanggal' => '2026-10-23',
                'cabang_id' => null, // Seluruh Cabang
                'status' => 'pending',
                'keterangan' => 'Gotong royong pembersihan alat deep fryer, etalase kasir, dan lantai dapur.',
            ]
        );

        // 2. Agenda Libur Kios (Maintenance Listrik - Rabu, 28 Oktober 2026)
        if ($cabang) {
            AgendaLiburToko::updateOrCreate(
                ['nama_agenda' => 'Libur Kios Pemeliharaan Instalasi Listrik'],
                [
                    'jenis' => 'libur_kios',
                    'tanggal' => '2026-10-28',
                    'cabang_id' => $cabang->id,
                    'status' => 'pending',
                    'keterangan' => 'Pemadaman terencana PLN dan perbaikan instalasi listrik kios.',
                ]
            );
        }
    }
}
