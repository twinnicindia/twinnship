<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SellerInvoiceExport implements FromCollection, WithHeadings {
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
            'Invoice Id',
            'Invoice Date',
            'Due Date',
            'Total Amount'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $invoices = DB::table('invoice')
            ->select(
                "sellers.code as 'Seller Code'",
                DB::raw("concat(sellers.first_name, sellers.last_name) as 'Seller Name'"),
                "invoice.inv_id as 'Invoice Id'",
                "invoice.invoice_date as 'Invoice Date'",
                "invoice.due_date as 'Due Date'",
                "invoice.total as 'Total Amount'",
            )
            ->join('sellers', 'invoice.seller_id', '=', 'sellers.id');

        if(!empty($this->filter['sellerId'])) {
            $invoices = $invoices->where('invoice.seller_id', $this->filter['sellerId']);
        }
        if(!empty($this->filter['fromDate'])) {
            $invoices = $invoices->whereDate('invoice.invoice_date', '>=', $this->filter['fromDate']);
        }
        if(!empty($this->filter['toDate'])) {
            $invoices = $invoices->whereDate('invoice.invoice_date', '<=', $this->filter['toDate']);
        }
        return $invoices->orderBy('invoice.invoice_date', 'desc')->get();
    }
}
