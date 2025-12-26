<?php

namespace App\Exports;

use App\Models\Partners;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class SellerRateCardSampleFileExport implements FromCollection, WithHeadings, WithStrictNullComparison {
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
        return array_keys(!empty($this->collection()->first()) ? $this->collection()->first() : []);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $sample = collect([]);
        $partners = Partners::all();
        foreach ($partners as $partner) {
            $sample->push([
                'courier' => $partner->title,
                'partner_id' => $partner->id,
                'plan_id' => $this->filter['plan_id'] ?? null,
                'seller_id' => $this->filter['seller_id'] ?? null,
                'within_city' => 0,
                'within_state' => 0,
                'metro_to_metro' => 0,
                'rest_india' => 0,
                'north_j_k' => 0,
                'cod_charge' => 0,
                'cod_maintenance' => 0,
                'extra_charge_a' => 0,
                'extra_charge_b' => 0,
                'extra_charge_c' => 0,
                'extra_charge_d' => 0,
                'extra_charge_e' => 0,
            ]);
        }
        return $sample;
    }
}
