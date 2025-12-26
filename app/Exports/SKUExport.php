<?php

namespace App\Exports;

use App\Models\SKU;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SKUExport implements FromCollection, WithHeadings {
    /**
     * Filter criteria
     * 
     * @var array
     */
    protected $filter;

    public function __construct(array $filter = []) {
        $this->filter = $filter;
    }

    /**
     * Set export header
     * 
     * @return array
     */
    public function headings(): array {
        return [
            'Product SKU',
            'Product Name',
            'Weight',
            'Length',
            'Breadth',
            'Height'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $sellers = DB::table('sku')
            ->select(
                "sku",
                "product_name",
                "weight",
                "length",
                "width",
                "height"
            );

        if(!empty($this->filter['id'])) {
            $sellers = $sellers->where('id', $this->filter['id']);
        }
        if(!empty($this->filter['sellerId'])) {
            $sellers = $sellers->where('seller_id', $this->filter['sellerId']);
        }
        $sellers = $sellers->orderBy('id', 'desc')
            ->get();
        return $sellers;
    }
}
