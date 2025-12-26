<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class SKUSampleFileExport implements FromCollection, WithHeadings, WithStrictNullComparison {
    /**
     * Set export header
     * 
     * @return array
     */
    public function headings(): array {
        return array_keys(!empty($this->collection()->first()) ? $this->collection()->first() : []);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect([
            [
                'seller_id' => '1',
                'product_sku' => 'SKU123',
                'product_name' => 'ABC',
                'product_weight' => '10',
                'product_length' => '10',
                'product_width' => '15',
                'product_height' => '15',
            ]
        ]);
    }
}
