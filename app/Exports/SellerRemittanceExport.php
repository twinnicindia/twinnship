<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SellerRemittanceExport implements FromCollection, WithHeadings {
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
        $remittances = DB::table('cod_transactions')
            ->select(
                "sellers.code as 'Seller Code'",
                DB::raw("concat(sellers.first_name, sellers.last_name) as 'Seller Name'"),
                "cod_transactions.id as 'Transaction Id'",
                "cod_transactions.datetime as 'Transaction Date'",
                "cod_transactions.amount as 'Amount'",
                "cod_transactions.description as 'Description'",
            )
            ->join('sellers', 'sellers.id', '=', 'cod_transactions.seller_id')
            ->where('cod_transactions.redeem_type', 'r');

        if(!empty($this->filter['sellerId'])) {
            $remittances = $remittances->where('cod_transactions.seller_id', $this->filter['sellerId']);
        }
        if(!empty($this->filter['fromDate'])) {
            $remittances = $remittances->whereDate('cod_transactions.datetime', '>=', $this->filter['fromDate']);
        }
        if(!empty($this->filter['toDate'])) {
            $remittances = $remittances->whereDate('cod_transactions.datetime', '<=', $this->filter['toDate']);
        }
        return $remittances->orderBy('cod_transactions.datetime', 'desc')->get();
    }
}
