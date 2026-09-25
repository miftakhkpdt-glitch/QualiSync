<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterItem;

class MasterItemSeeder extends Seeder
{
    public function run(): void
    {
        MasterItem::updateOrCreate(
            ['no_mm' => '310132'], 
            [
                'item_name'     => 'TUBE KAHF BRIGHT REVITALIZING AMINOGEL FACE WASH 10 ML',
                'min_weight'    => 17.00, 
                'max_weight'    => 18.00, 
                'min_height'    => 141.20,
                'max_height'    => 144.20,
                'min_lof'       => 0.40,
                'max_lof'       => 2.00,
                'sep_force'     => '10.00 Kgf',
                'std_air_tight' => '0,6 BAR (5 menit)',
                'std_tape_test' => '3M 610',
                'std_unzip'     => '-', // Sesuai dokumen (bersifat OK/NG)
                'std_pinch'     => '-', // Sesuai dokumen (bersifat OK/NG)
            ]
        );
    }
}