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
        // 1. Shift 1
        MasterShift::updateOrCreate(
            ['nama_shift' => 'Shift 1'],
            [
                'jam_masuk' => '06:00:00',
                'jam_pulang' => '14:00:00',
                'lintas_hari' => false
            ]
        );

        // 2. Shift 2
        MasterShift::updateOrCreate(
            ['nama_shift' => 'Shift 2'],
            [
                'jam_masuk' => '14:00:00',
                'jam_pulang' => '22:00:00',
                'lintas_hari' => false
            ]
        );

        // 3. Shift 3
        MasterShift::updateOrCreate(
            ['nama_shift' => 'Shift 3'],
            [
                'jam_masuk' => '22:00:00',
                'jam_pulang' => '06:00:00',
                'lintas_hari' => true
            ]
        );
    }
}