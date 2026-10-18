<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterShift;

class MasterShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Shift Pagi
        MasterShift::firstOrCreate(
            ['nama_shift' => 'Shift Pagi'],
            [
                'jam_masuk' => '06:00:00',
                'jam_pulang' => '14:00:00',
                'lintas_hari' => false
            ]
        );

        // 2. Shift Sore
        MasterShift::firstOrCreate(
            ['nama_shift' => 'Shift Sore'],
            [
                'jam_masuk' => '14:00:00',
                'jam_pulang' => '22:00:00',
                'lintas_hari' => false
            ]
        );
    }
}
