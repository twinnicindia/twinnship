<?php

namespace App\Exports;

use App\Models\Partners;
use App\Models\Rates;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class SellerRateCardExport implements FromCollection, WithHeadings, WithStrictNullComparison {
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
        return array_keys(!empty($this->collection()->first()) ? $this->collection()->first()->toArray() : []);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        $planId = $this->filter['plan_id'] ?? "";
        $sellerId = $this->filter['seller_id'] ?? 0;
        $rates = Partners::leftJoin('rates',function ($join) use($planId,$sellerId){
            $join->on('rates.partner_id','partners.id');
            $join->where('rates.seller_id',$sellerId);
            if(!empty($planId))
                $join->where('rates.plan_id',$planId);
        })->select('partners.title as courier','partners.id as partner_id',DB::raw("{$planId} as plan_id"),DB::raw("{$sellerId} as seller_id"),'rates.within_city','rates.within_state','rates.metro_to_metro','rates.rest_india','rates.north_j_k','rates.cod_charge','rates.cod_maintenance','rates.extra_charge_a','rates.extra_charge_b','rates.extra_charge_c','rates.extra_charge_d','rates.extra_charge_e');
        return $rates->where('partners.status','y')->get();
    }
}
