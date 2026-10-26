<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CabangKantor;

class CabangKantorSeeder extends Seeder
{
    public function run(): void
    {
        $cabang = [
            [
                'nama_cabang' => 'Lazatto Cijeruk Chicken And Burger',
                'latitude' => '-6.169537365875819',
                'longitude' => '106.3087684945983',
                'radius_meter' => 100,
                'is_24_jam' => false
            ],
            [
                'nama_cabang' => 'Lazatto Ciruas Chicken and Burger',
                'latitude' => '-6.124596293960915',
                'longitude' => '106.23515367514217',
                'radius_meter' => 100,
                'is_24_jam' => true
            ],
            [
                'nama_cabang' => 'Lazatto Cikande Ambon Chicken and Burger',
                'latitude' => '-6.2105246826536025',
                'longitude' => '106.35878580252626',
                'radius_meter' => 100,
                'is_24_jam' => true
            ],
            [
                'nama_cabang' => 'Lazatto Link Barokah Chicken and Burger',
                'latitude' => '-6.0151632930727486',
                'longitude' => '106.06378469830703',
                'radius_meter' => 100,
                'is_24_jam' => false
            ],
            [
                'nama_cabang' => 'Lazatto Link Terondol Chicken and Burger',
                'latitude' => '-6.1002473',
                'longitude' => '106.1792244',
                'radius_meter' => 100,
                'is_24_jam' => false
            ],
        ];

        foreach ($cabang as $data) {
            CabangKantor::updateOrCreate(
                ['nama_cabang' => $data['nama_cabang']],
                [
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'radius_meter' => $data['radius_meter'],
                    'is_24_jam' => $data['is_24_jam'] ?? false,
                ]
            );
        }
    }
}