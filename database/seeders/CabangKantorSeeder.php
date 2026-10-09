<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CabangKantor;

class CabangKantorSeeder extends Seeder
{
    public function run(): void
    {
        CabangKantor::firstOrCreate(
            ['nama_cabang' => 'Kantor Pusat Jakarta'],
            [
                'latitude' => '-6.2088',
                'longitude' => '106.8456',
                'radius_meter' => 100
            ]
        );

        CabangKantor::firstOrCreate(
            ['nama_cabang' => 'Cabang Bandung'],
            [
                'latitude' => '-6.9175',
                'longitude' => '107.6191',
                'radius_meter' => 150
            ]
        );

        CabangKantor::firstOrCreate(
            ['nama_cabang' => 'Cabang Surabaya'],
            [
                'latitude' => '-7.2575',
                'longitude' => '112.7521',
                'radius_meter' => 200
            ]
        );
    }
}
