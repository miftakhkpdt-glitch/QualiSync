<?php

namespace App\Imports;

use App\Models\MasterItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MasterItemsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return MasterItem::updateOrCreate(
            ['no_mm' => $row['no_mm']], // Kolom acuan unik
            [
                'item_name'      => $row['item_name'],
                'min_weight'     => $row['min_weight'],
                'max_weight'     => $row['max_weight'],
                'min_height'     => $row['min_height'],
                'max_height'     => $row['max_height'],
                'min_lof'        => $row['min_lof'],
                'max_lof'        => $row['max_lof'],
                'sep_force'      => $row['sep_force'],
                'std_air_tight'  => $row['std_air_tight'],
                'std_tape_test'  => $row['std_tape_test'],
                'std_unzip'      => $row['std_unzip'],
                'std_pinch'      => $row['std_pinch'],
            ]
        );
    }
}