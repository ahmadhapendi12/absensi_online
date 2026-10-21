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
                'radius_meter' => 100
            ],
            [
                'nama_cabang' => 'Lazatto Ciruas Chicken and Burger',
                'latitude' => '-6.124596293960915',
                'longitude' => '106.23515367514217',
                'radius_meter' => 100
            ],
            [
                'nama_cabang' => 'Lazatto Cikande Ambon Chicken and Burger',
                'latitude' => '-6.2105246826536025',
                'longitude' => '106.35878580252626',
                'radius_meter' => 100
            ],
            [
                'nama_cabang' => 'Lazatto Link Barokah Chicken and Burger',
                'latitude' => '-6.0151632930727486',
                'longitude' => '106.06378469830703',
                'radius_meter' => 100
            ],
            [
                // Titik Testing di Rumah
                'nama_cabang' => 'Warung Mamah Nazwa (Titik Testing)',
                'latitude' => '-6.178047369966342',
                'longitude' => '106.15891580438061',
                'radius_meter' => 100 
            ],
        ];

        foreach ($cabang as $data) {
            // Karena ini firstOrCreate, dia akan ngecek nama cabangnya dulu
            // Kalau nama cabangnya udah ada di DB, datanya nggak akan ketimpa
            // Kalau mau update radius yang udah masuk, bisa pakai updateOrCreate
            CabangKantor::updateOrCreate(
                ['nama_cabang' => $data['nama_cabang']],
                [
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'radius_meter' => $data['radius_meter']
                ]
            );
        }
    }
}