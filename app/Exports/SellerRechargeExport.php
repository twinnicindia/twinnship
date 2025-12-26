<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SellerRechargeExport implements FromCollection, WithHeadings {
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
            'Seller Code',
            'Seller Name',
            'Transaction Id',
            'Transaction Date',
            'Amount',
            'Description'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $recharges = DB::table('transactions')
            ->select(
                "sellers.code as 'Seller Code'",
                DB::raw("concat(sellers.first_name, sellers.last_name) as 'Seller Name'"),
                "transactions.id as 'Transaction Id'",
                "transactions.datetime as 'Transaction Date'",
                "transactions.amount as 'Amount'",
                "transactions.description as 'Description'",
            )
            ->join('sellers', 'sellers.id', '=', 'transactions.seller_id')
            ->where('transactions.redeem_type', 'r');

        if(!empty($this->filter['sellerId'])) {
            $recharges = $recharges->where('transactions.seller_id', $this->filter['sellerId']);
        }
        if(!empty($this->filter['fromDate'])) {
            $recharges = $recharges->whereDate('transactions.datetime', '>=', $this->filter['fromDate']);
        }
        if(!empty($this->filter['toDate'])) {
            $recharges = $recharges->whereDate('transactions.datetime', '<=', $this->filter['toDate']);
        }
        return $recharges->orderBy('transactions.datetime', 'desc')->get();
    }
}
