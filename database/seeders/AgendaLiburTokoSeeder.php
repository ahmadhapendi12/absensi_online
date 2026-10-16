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
        $cabang = CabangKantor::first();

        // 1. Agenda Jumsih (Jumat Bersih)
        AgendaLiburToko::create([
            'nama_agenda' => 'Jumsih Rutin (Jumat Bersih Outlet)',
            'jenis' => 'jumsih',
            'tanggal' => Carbon::now()->addDays(2)->toDateString(),
            'cabang_id' => null, // Seluruh Cabang
            'status' => 'pending',
            'keterangan' => 'Gotong royong pembersihan alat deep fryer, etalase kasir, dan lantai dapur.',
        ]);

        // 2. Agenda Libur Kios (Maintenance Listrik)
        if ($cabang) {
            AgendaLiburToko::create([
                'nama_agenda' => 'Libur Kios Pemeliharaan Instalasi Listrik',
                'jenis' => 'libur_kios',
                'tanggal' => Carbon::now()->addDays(5)->toDateString(),
                'cabang_id' => $cabang->id,
                'status' => 'pending',
                'keterangan' => 'Pemadaman terencana PLN dan perbaikan instalasi listrik kios.',
            ]);
        }
    }
}
